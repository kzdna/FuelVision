<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Support\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'nama' => 'Admin Finance',
                'email' => 'admin@company.com',
                'role' => RoleName::ADMIN_FINANCE,
            ],
            [
                'nama' => 'Vendor',
                'email' => 'vendor@company.com',
                'role' => RoleName::VENDOR,
            ],
            [
                'nama' => 'View Only',
                'email' => 'viewer@company.com',
                'role' => RoleName::VIEW_ONLY,
            ],
        ];

        foreach ($accounts as $account) {
            $role = Role::where('nama_role', $account['role'])->first();

            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'role_id' => $role->id,
                    'nama' => $account['nama'],
                    'password' => Hash::make('password'),
                    'status' => true,
                ]
            );
        }
    }
}