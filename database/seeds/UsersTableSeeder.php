<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate Users table
        User::truncate();

        $password = Hash::make('gmradmin');

        User::create([
            'name' => 'GMR',
            'email' => 'gmr@test.com',
            'password' => $password,
        ]);
    }
}
