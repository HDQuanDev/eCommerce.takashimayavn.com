<?php

namespace Database\Seeds;  // Đảm bảo có namespace này

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PosPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['pos_manager', 'pos_configuration'];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $user = User::find(9);

        if ($user) {
            $user->givePermissionTo($permissions);
            echo "Đã gán quyền POS cho user: {$user->email}\n";
        } else {
            echo "User không tồn tại.\n";
        }
    }
}
