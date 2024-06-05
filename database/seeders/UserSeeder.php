<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            $roleId = rand(1, 3); // random roles_id between 1 and 3
            $name = "User " . $i;
            $email = "user" . $i . "@example.com";
            $password = "test1234"; // default password

            User::factory()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'roles_id' => $roleId,
            ]);
        }
    }
}
