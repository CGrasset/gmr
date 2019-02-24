<?php

use App\User;
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
        $password = Hash::make('gmradmin');

        User::create([
            'name' => 'GMR',
            'email' => 'gmr@test.com',
            'password' => $password,
        ]);
    }
}
