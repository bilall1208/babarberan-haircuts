<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class KasirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'kasir@babarberan.test'],
            [
                'name'     => 'Kasir Babarberan',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role'     => 'kasir',
            ]
        );
    }
}
