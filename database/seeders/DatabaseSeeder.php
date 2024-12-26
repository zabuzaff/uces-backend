<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->registerUser();
    }

    public function registerUser()
    {
        $datas = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Passenger',
                'email' => 'passenger@example.com',
                'password' => bcrypt('password'),
                'role' => 'passenger'
            ],
            [
                'name' => 'Driver',
                'email' => 'driver@example.com',
                'password' => bcrypt('password'),
                'role' => 'driver'
            ],
        ];

        foreach ($datas as $data) {
            DB::table('users')->insert($data);
        }
    }
}
