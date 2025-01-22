<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $date_now = Carbon::now();

        Role::insert([
            ['name' => 'Admin', 'created_at' => $date_now, 'updated_at' => $date_now],
            ['name' => 'Viewer', 'created_at' => $date_now, 'updated_at' => $date_now],
        ]);
    }
}
