<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::controller(WebController::class)->group(function () {

    Route::get('/', 'home')->name('home');

    Route::get('/scripts/{name}', 'script')
        ->name('scripts');

    Route::post('/session/upload', 'upload')
        ->name('session.upload')
        ->middleware(['throttle:upload']);

    Route::delete('session/delete/{upload}', 'delete')->name('session.delete');

});
