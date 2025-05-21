<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Jhonatan Guerrero', 'email' => 'jhonatanguerrero@outlook.com', 'role' => 'admin'],
            ['name' => 'Camilo Murcia', 'email' => 'camilomurcia@outlook.com', 'role' => 'coordinator'],
            ['name' => 'Andres Garcia', 'email' => 'andresgarcia@outlook.com', 'role' => 'auxiliar'],
            ['name' => 'Maria Gonzalez', 'email' => 'mariagonzalez@outlook.com', 'role' => 'visitor'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate([
                'email' => $data['email']
            ], [
                'name' => $data['name'],
                'password' => Hash::make('Password01*'),
            ]);

            $user->assignRole($data['role']);
        }
    }
}
