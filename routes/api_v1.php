<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::get('/', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::delete('/', 'destroy')->name('destroy')->middleware('auth:sanctum');
    });

Route::controller(BookController::class)
    ->prefix("books")
    ->name("books.")
    ->middleware([])
    ->group(function () {
        Route::get("/", "index")->name("index");
        Route::get("/{id}", "show")->name("show");
        Route::post("/", "store")->name("store")->middleware(["auth:sanctum"]);
        Route::put("/{id}", "update")->name("update")->middleware(["auth:sanctum"]);
        Route::delete("/{id}", "destroy")->name("destroy")->middleware(["auth:sanctum"]);
    });

Route::controller(CategoryController::class)
    ->prefix("categories")
    ->name("categories.")
    ->group(function () {
        Route::get("/", "index")->name("index");
    });
