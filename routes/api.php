<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::controller(ApiController::class)->group(function () {

    Route::put('/{filename}', 'upload')
        ->name('api.upload')
        ->middleware(['throttle:upload']);

    Route::prefix('/{upload}')->whereUuid('upload')->group(function () {

        Route::get('/{filename?}', 'download')
            ->name('api.download')
            ->middleware(['throttle:download']);

        Route::delete('/{filename?}', 'delete')
            ->name('api.delete');

    });

});
