<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NFTController extends Controller
{
    /**
     * Get collection details from OpenSea with caching and error handling.
     */
    public function getCollectionDetails(Request $request)
    {
        $collectionSlug = $request->get('slug', 'cyberpastnft');
        $cacheKey = "opensea_collection_{$collectionSlug}";
        $cacheDuration = 3600; // Cache for 1 hour

        try {
            $data = Cache::remember($cacheKey, $cacheDuration, function () use ($collectionSlug) {
                $apiKey = config('services.opensea.key');

                if (empty($apiKey)) {
                    Log::error('OpenSea API key is missing from configuration.');
                    throw new \Exception('API Configuration Error', 500);
                }

                $response = Http::withHeaders([
                    'X-API-KEY' => $apiKey,
                    'accept' => 'application/json',
                ])->timeout(10)->get("https://api.opensea.io/api/v2/collections/{$collectionSlug}");

                if ($response->failed()) {
                    Log::warning("OpenSea API request failed for slug: {$collectionSlug}", [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                    
                    if ($response->status() === 404) {
                        throw new \Exception('Collection not found', 404);
                    }

                    throw new \Exception('Failed to fetch data from OpenSea', $response->status());
                }

                return $response->json();
            });

            return response()->json($data);

        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            // Ensure status is a valid HTTP status code
            if ($status < 100 || $status > 599) $status = 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Get collection assets from OpenSea with caching, search, and floor price.
     */
    public function getAssets(Request $request)
    {
        $collectionSlug = $request->get('slug', 'cyberpastnft');
        $limit = $request->get('limit', 50);
        $search = $request->get('search');
        
        $cacheKey = "opensea_assets_full_{$collectionSlug}";
        $cacheDuration = 1800; // Cache for 30 minutes for better responsiveness

        try {
            $data = Cache::remember($cacheKey, $cacheDuration, function () use ($collectionSlug) {
                $apiKey = config('services.opensea.key');

                if (empty($apiKey)) {
                    Log::error('OpenSea API key is missing from configuration.');
                    throw new \Exception('API Configuration Error', 500);
                }

                // 1. Fetch NFTs (getting up to 50 for initial display/search)
                $nftsResponse = Http::withHeaders([
                    'X-API-KEY' => $apiKey,
                    'accept' => 'application/json',
                ])->timeout(10)->get("https://api.opensea.io/api/v2/collection/{$collectionSlug}/nfts", [
                    'limit' => 50,
                ]);

                if ($nftsResponse->failed()) {
                    throw new \Exception('Failed to fetch assets from OpenSea', $nftsResponse->status());
                }

                $nftsData = $nftsResponse->json();

                // 2. Fetch Collection Details for Floor Price
                $collectionResponse = Http::withHeaders([
                    'X-API-KEY' => $apiKey,
                    'accept' => 'application/json',
                ])->timeout(10)->get("https://api.opensea.io/api/v2/collections/{$collectionSlug}");

                $floorPrice = 'Check on OpenSea';
                if ($collectionResponse->successful()) {
                    $colData = $collectionResponse->json();
                    
                    // OpenSea v2 collection stats
                    if (isset($colData['total_supply'])) {
                        // If root is the collection object
                        $stats = $colData;
                    } elseif (isset($colData['collection'])) {
                        $stats = $colData['collection'];
                    }

                    if (isset($stats)) {
                        // In some v2 versions, floor price is in stats or similar
                        // Let's look for common keys
                        $fp = $stats['floor_price'] ?? null;
                        if ($fp) {
                            $symbol = $stats['floor_price_symbol'] ?? 'ETH';
                            $floorPrice = round($fp, 4) . ' ' . $symbol;
                        }
                    }
                }

                return [
                    'nfts' => $nftsData['nfts'] ?? [],
                    'next' => $nftsData['next'] ?? null,
                    'floor_price' => $floorPrice,
                ];
            });

            // Handle backend search if query provided
            if ($search) {
                $search = strtolower($search);
                $data['nfts'] = array_filter($data['nfts'], function ($nft) use ($search) {
                    $name = strtolower($nft['name'] ?? '');
                    $id = strtolower($nft['identifier'] ?? '');
                    return str_contains($name, $search) || str_contains($id, $search);
                });
                // Reset keys after filtering
                $data['nfts'] = array_values($data['nfts']);
            }

            // Apply limit if requested (after search)
            if ($limit < count($data['nfts'])) {
                $data['nfts'] = array_slice($data['nfts'], 0, $limit);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('NFT API Error: ' . $e->getMessage());
            $status = $e->getCode() ?: 500;
            if ($status < 100 || $status > 599) $status = 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $status);
        }
    }
}
