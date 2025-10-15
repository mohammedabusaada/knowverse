<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tag;

// NOTE: This seeder is based on an older schema version.
// It will be updated to match the final database structure soon.
class PostSeeder extends Seeder
{
    public function run(): void
    {
        // 30 Posts
        Post::factory()->count(30)->create()->each(function ($post) {
            // Attach 1‑3 Tags with every post
            $tags = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $post->tags()->attach($tags);
        });
    }
}
