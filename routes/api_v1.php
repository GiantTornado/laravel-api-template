<?php

use App\Features\Book\MostExpensiveBookFeature;
use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Common\ExcelExportController;
use App\Http\Controllers\Api\V1\Common\ImageUploadController;
use App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Api\V1\Common\AuthController;
use App\Http\Controllers\Api\V1\Common\BookController;
use App\Http\Controllers\Api\V1\Common\CategoryController;
use App\Http\Controllers\Api\V1\Common\PennantBookController;
use App\Http\Controllers\Api\V1\Common\SocialLoginController;
use App\Http\Controllers\Api\V1\Common\UserController;
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

Route::controller(SocialLoginController::class)
    ->prefix('auth/social')
    ->name('auth.social.')
    ->group(function () {
        Route::get('redirect/{provider}', 'redirectToProvider')
            ->where('driver', implode('|', config('auth.socialite.drivers')))
            ->name('login');

        Route::get('{provider}/callback', 'handleProviderCallback')
            ->where('provider', implode('|', config('auth.socialite.drivers')))
            ->name('callback');
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

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::controller(Admin\TaskController::class)
            ->prefix('tasks')
            ->name('tasks.')
            ->group(function () {
                Route::get('/', 'index')->name('index')->middleware(['auth:sanctum']);
            });

    });

Route::prefix('user')
    ->name('user.')
    ->group(function () {

        Route::controller(User\TaskController::class)
            ->prefix('tasks')
            ->name('tasks.')
            ->group(function () {
                Route::get('/', 'index')->name('index')->middleware(['auth:sanctum']);
            });

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

Route::controller(ImageUploadController::class)
    ->prefix('images')
    ->name('images.')
    ->group(function () {
        Route::post('/s3', 'storeS3')->name('store.s3');
    });

Route::controller(ExcelExportController::class)
    ->prefix('excel')
    ->name('excel.')
    ->group(function () {
        Route::get('/books', 'export')->name('export');
    });