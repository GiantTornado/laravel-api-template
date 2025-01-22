<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $users = config('seeds.users');
        foreach ($users as $user) {
            $created_user = User::create([
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => $user['password'],
                'role_id' => $user['role_id'],
            ]);
            $created_user->profile()->create([
                'first_name' => $user['profile']['first_name'],
                'last_name' => $user['profile']['last_name'],
            ]);
        }
    }
}
