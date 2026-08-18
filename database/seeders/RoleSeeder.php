<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\RoleName;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleName::all() as $roleName) {
            Role::firstOrCreate(['nama_role' => $roleName]);
        }
    }
}
