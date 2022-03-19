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
            'username' => 'Belhedi',
            'password' => bcrypt('Rafaa94$'),
            'birthdate' => Carbon::parse('1994-04-15'),
            'sexe' => 'M',
            'phone' => '41056519',
            'avatar' => 'my-image.png',
            'is_admin' => true,
            'is_banned' => false,
            'role_id' => 1,
            'country_id' => 227
        ]);
    }
}
