<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (\App::environment('local')) {
            User::updateOrCreate([
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'admin@ejmastnak.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'is_registered' => true,
                'is_paying' => false,
                'is_admin' => true,
            ]);
            User::updateOrCreate([
                'name' => 'registered',
                'username' => 'registered',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'is_registered' => true,
                'is_paying' => false,
                'is_admin' => false,
            ]);
            User::updateOrCreate([
                'name' => 'paying',
                'username' => 'paying',
                'email' => 'paying@ejmastnak.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'is_registered' => true,
                'is_paying' => true,
                'is_admin' => false,
            ]);
        }

        if (\App::environment('production')) {
            User::updateOrCreate([
                'name' => 'Elijan Mastnak',
                'username' => 'ejmastnak',
                'email' => 'accounts@ejmastnak.com',
                'password' => '$2y$10$KB7bn6XBlFL9B4P3Fc8RxeUUO8kmT.vS4OjCkDhGKJRgLlSbePiZK',
                'is_registered' => true,
                'is_paying' => true,
                'is_admin' => false,
            ]);
            User::updateOrCreate([
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@ejmastnak.com',
                'password' => '$2y$10$KB7bn6XBlFL9B4P3Fc8RxeUUO8kmT.vS4OjCkDhGKJRgLlSbePiZK',
                'is_registered' => true,
                'is_paying' => true,
                'is_admin' => true,
            ]);
        }
    }
}
