<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
     public function run()
     {

        // Seed a default administrator
        User::create([
            'first_name' => 'Default',
            'last_name' => 'Admin',
            'email' => 'default@local.host',
            'username' => 'admin',
            'password' => password_hash('admin', PASSWORD_BCRYPT),
            'role' => 'ADMIN',
        ]);

        // Seed an administrator for Shawn
        User::create([
            'first_name' => 'Shawn',
            'last_name' => '',
            'email' => 'sh@w.n',
            'username' => 'shawn',
            'password' => password_hash('shawn', PASSWORD_BCRYPT),
            'role' => 'ADMIN',
        ]);

        // Seed some test users
        User::create([
            'first_name' => 'Content',
            'last_name' => 'User',
            'email' => "content@lum.en",
            'username' => "content",
            'password' => password_hash('content', PASSWORD_BCRYPT),
            'role' => 'CONTENTADMIN',
        ]);

        User::create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => "user@lum.en",
            'username' => "second",
            'password' => password_hash('second', PASSWORD_BCRYPT),
            'role' => 'USER',
        ]);

    }
}
