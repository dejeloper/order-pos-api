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
            ['name' => 'dejeloper', 'username' => 'dejeloper', 'role' => 'developer'],
            ['name' => 'Jhonatan Guerrero', 'username' => 'jhonatan', 'role' => 'admin'],
            ['name' => 'Camilo Murcia', 'username' => 'camilo', 'role' => 'coordinator'],
            ['name' => 'Marta Garcia', 'username' => 'marta', 'role' => 'auxiliar'],
            ['name' => 'Maria Gonzalez', 'username' => 'maria', 'role' => 'visitor'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate([
                'username' => $data['username']
            ], [
                'name' => $data['name'],
                'password' => Hash::make('Password01*'),
            ]);

            $user->assignRole($data['role']);
        }
    }
}
