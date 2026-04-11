<?php

use App\Http\Controllers\Api\V1\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('api.v1.')->group(function () {
    Route::post('/subscribers', [SubscriberController::class, 'store'])->name('subscribers.store');
    Route::get('/subscribers/confirm/{token}', [SubscriberController::class, 'confirm'])->name('subscribers.confirm');
    Route::delete('/subscribers/{id}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');
});
