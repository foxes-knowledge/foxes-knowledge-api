<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
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
        $data = [
            [
                'name' => 'PAXANDDOS',
                'email' => 'pashalitovka@gmail.com',
                'password' => Hash::make('123456789')
            ],
            [
                'name' => 'navwie',
                'email' => 'navwie@gmail.com',
                'password' => Hash::make('123456789')
            ]
        ];

        foreach ($data as $user) {
            User::create($user);
        }

        User::factory()->count(UserFactory::NUMBER + count($data))->create();
    }
}
