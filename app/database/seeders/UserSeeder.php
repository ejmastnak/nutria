<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->delete();
        DB::table('users')->truncate();

        User::create([
        'name' => 'admin',
        'email' => 'admin@ejmastnak.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'is_free_tier' => false,
        'is_full_tier' => true,
        'is_admin' => true,
        ]);

        User::create([
        'name' => 'free',
        'email' => 'free@ejmastnak.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'is_free_tier' => true,
        'is_full_tier' => false,
        'is_admin' => false,
        ]);

        User::create([
        'name' => 'full',
        'email' => 'full@ejmastnak.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'is_free_tier' => false,
        'is_full_tier' => true,
        'is_admin' => false,
        ]);

    }
}
