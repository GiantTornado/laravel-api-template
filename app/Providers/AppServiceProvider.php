<?php

namespace App\Providers;

use App\Repositories\Cache\CachedCategoryRepository;
use App\Repositories\Database\DatabaseCategoryRepository;
use App\Repositories\Database\DatabaseProfileRepository;
use App\Repositories\Database\DatabaseUserRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(CategoryRepositoryInterface::class, function () {
            return new CachedCategoryRepository(new DatabaseCategoryRepository);
        });

        $this->app->bind(UserRepositoryInterface::class, function () {
            return new DatabaseUserRepository;
        });

        $this->app->bind(ProfileRepositoryInterface::class, function () {
            return new DatabaseProfileRepository;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // Password default rules when calling [Password::defaults()] as in [StoreAuthRequest]
        Password::defaults(fn () => Password::min(8)->letters()->mixedCase()->numbers());

        EnsureFeaturesAreActive::whenInactive(
            function (Request $request, array $features) {
                return response()->json(['message' => 'Feature is in-active.'], 403);
            }
        );
    }
}
