<?php

namespace App\Features\Book;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Support\Lottery;

/*
    Intallation:
        composer require laravel/pennant
        php artisan vendor:publish --provider="Laravel\Pennant\PennantServiceProvider"
        php artisan migrate

    Create Class:
        php artisan pennant:feature [name]
*/

class MostExpensiveBookFeature {
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(User $user): mixed {
        return match (true) {
            $user->role_id === RolesEnum::ADMIN->value => false,
            app()->environment('staging') => true,
            default => Lottery::odds(1, 5),
        };
    }
}
