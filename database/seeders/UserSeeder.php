<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'jeramirez@tiendaregistrada.com.co',
            'password' => Hash::make('Makeitshine12345*'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole(RolesEnum::ADMINISTRATOR->value);

        $admin = User::create([
            'name' => 'Ejecutivo',
            'email' => 'julian.ramirez@02hotmail.com',
            'password' => Hash::make('Makeitshine12345*'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole(RolesEnum::ACCOUNT_EXECUTIVE->value);
    }
}
