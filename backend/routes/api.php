<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('api.v1.')->group(function () {
    Route::post('/subscribers', [SubscriberController::class, 'store'])->name('subscribers.store');
    Route::get('/subscribers/confirm/{token}', [SubscriberController::class, 'confirm'])->name('subscribers.confirm');
    Route::delete('/subscribers/{id}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
        Route::get('/newsletters/{newsletter}/stats', [NewsletterController::class, 'stats'])->name('newsletters.stats');
        Route::post('/newsletters/send', [NewsletterController::class, 'send'])->name('newsletters.send');
    });
});
