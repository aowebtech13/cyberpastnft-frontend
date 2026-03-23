<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Store newsletter subscription email in a txt file.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $email = $request->input('email');
            $filePath = 'subscriptions.txt';
            $content = $email . " | " . now()->toDateTimeString() . PHP_EOL;

            // Store in storage/app/subscriptions.txt
            Storage::append($filePath, $content);

            return response()->json([
                'success' => true,
                'message' => 'Added to newsletter message'
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter Subscription Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe. Please try again later.'
            ], 500);
        }
    }
}
