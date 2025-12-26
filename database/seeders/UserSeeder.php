<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // Di UserSeeder.php
public function run()
{
    $roles = DB::table('roles')->pluck('id', 'name')->toArray();

    DB::table('users')->insert([
        [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['Administrator'],
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['Konservasionis Lapangan'],
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Peneliti User',
            'email' => 'peneliti@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['Peneliti Ekologi'],
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['Pengambil Kebijakan'],
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}
}
