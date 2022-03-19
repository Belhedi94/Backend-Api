<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Rafaa',
            'last_name' => 'Belhedi',
            'email' => 'rafaa.b@gmail.com',
            'password' => bcrypt('Rafaa94$'),
            'username' => 'Belhedi',
            'is_admin' => true,
            'role_id' => 1,
            'is_banned' => false,
            'sexe' => 'M',
            'phone' => '41056519',
            'birthdate' => Carbon::parse('1994-04-15'),
            'avatar' => 'my-image.png'
        ]);
    }
}
