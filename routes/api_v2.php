<?php

use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;

Route::controller(BookController::class)
    ->prefix('books')
    ->name('books.')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });
