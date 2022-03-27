<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::create([
            'title' => 'My first post',
            'body' => 'this is my body',
            'cover_image' => 'cover.png',
            'user_id' => '1',
        ]);
    }
}
