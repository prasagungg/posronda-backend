<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Pos Ronda Official',
                'username' => 'posronda_official',
                'email' => 'posronda_official@yopmail.com',
                'password' => Hash::make('Posronda_123'),
            ],
        ];

        foreach ($users as $user) {
            User::UpdateOrCreate(['username' => $user['username']], $user);
        }
    }
}
