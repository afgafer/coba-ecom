<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'merah',
            'email' => 'merah@gmail.com',
            'password' => bcrypt('admin123')
        ]);
        User::create([
            'name' => 'jingga',
            'email' => 'jingga@gmail.com',
            'password' => bcrypt('admin123')
        ]);
        User::create([
            'name' => 'kuning',
            'email' => 'kuning@gmail.com',
            'password' => bcrypt('admin123')
        ]);
        User::create([
            'name' => 'hijau',
            'email' => 'hijau@gmail.com',
            'password' => bcrypt('admin123')
        ]);
        User::create([
            'name' => 'biru',
            'email' => 'biru@gmail.com',
            'password' => bcrypt('admin123')
        ]);
    }
}
