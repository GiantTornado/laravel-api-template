<?php

use App\Features\Book\MostExpensiveBookFeature;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\PennantBookController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::get('/', 'show')->name('show')->middleware('auth:sanctum');
        Route::post('/', 'store')->name('store');
        Route::delete('/', 'destroy')->name('destroy')->middleware('auth:sanctum');
    });

Route::controller(UserController::class)
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::post('/', 'store')->name('store');
    });

Route::controller(BookController::class)
    ->prefix('books')
    ->name('books.')
    ->middleware([])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store')->middleware(['auth:sanctum']);
        Route::put('/{id}', 'update')->name('update')->middleware(['auth:sanctum']);
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware(['auth:sanctum']);
    });

Route::controller(CategoryController::class)
    ->prefix('categories')
    ->name('categories.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store')->middleware(['auth:sanctum']);
        Route::put('/{id}', 'update')->name('update')->middleware(['auth:sanctum']);
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware(['auth:sanctum']);
    });

// Pennant: Features Flags
Route::controller(PennantBookController::class)
    ->prefix('pennant-books')
    ->name('pennant-books.')
    ->middleware([])
    ->group(function () {
        Route::get('/most-expensive-book', 'showMostExpensiveBook')->name('show.most-expensive-book')->middleware(['auth:sanctum', EnsureFeaturesAreActive::using(MostExpensiveBookFeature::class)]);
        Route::get('/cheapest-book', 'showCheapestBook')->name('show.cheapest-book')->middleware(['auth:sanctum']);
        Route::put('/activate-most-expensive-book', 'updateMostExpensiveBookActivation')->name('show.most-expensive-book-activation')->middleware(['auth:sanctum']);
    });
