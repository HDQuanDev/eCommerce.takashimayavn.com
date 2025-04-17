<?php

namespace Database\Seeders;  // Thay từ 'Seeds' thành 'Seeders'

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PosPermissionSeeder::class,
        ]);
    }
}
