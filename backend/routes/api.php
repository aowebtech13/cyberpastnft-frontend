<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NFTController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ContactController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/nft/collection', [NFTController::class, 'getCollectionDetails']);
    Route::get('/nft/assets', [NFTController::class, 'getAssets']);
    Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/contact', [ContactController::class, 'send']);
});
