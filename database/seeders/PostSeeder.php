<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        if (Tag::count() === 0) {
            $this->call(TagSeeder::class);
        }

        $users = User::all();
        $tags = Tag::all();

        // Group tags by category for diversity
        $tagCategories = [
            'Computer Science' => $tags->whereIn('name', ['AI','Web Security','Networking','Machine Learning','Cybersecurity','Cryptography','Blockchain','Cloud Computing','Databases','Operating Systems']),
            'Natural Sciences' => $tags->whereIn('name', ['Biology','Chemistry','Physics','Astronomy','Geology']),
            'Mathematics & Data' => $tags->whereIn('name', ['Algebra','Calculus','Statistics','Data Analysis','Big Data']),
            'Humanities & Social Sciences' => $tags->whereIn('name', ['Philosophy','Psychology','Sociology','Economics','History','Ethics']),
            'Logic / Critical Thinking' => $tags->whereIn('name', ['Logical Fallacies','Argumentation','Epistemology','Critical Thinking']),
        ];

        foreach ($users as $user) {
            $posts = Post::factory(rand(1, 5))->create(['user_id' => $user->id]);

            $posts->each(function ($post) use ($tagCategories) {
                $assignedTags = collect();

                // Pick 1–2 tags from different categories for diversity
                foreach ($tagCategories as $categoryTags) {
                    if ($categoryTags->count() && rand(0, 1)) { // 50% chance to include a tag from this category
                        $assignedTags->push($categoryTags->random()->id);
                    }
                }

                // Attach 1–3 total tags max
                $assignedTags = $assignedTags->unique()->take(rand(1, 3));

                $post->tags()->attach($assignedTags->toArray());
            });
        }
    }
}
