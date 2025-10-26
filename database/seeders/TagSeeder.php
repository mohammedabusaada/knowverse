<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Computer Science
            'AI', 'Web Security', 'Networking', 'Machine Learning', 'Cybersecurity',
            'Cryptography', 'Blockchain', 'Cloud Computing', 'Databases', 'Operating Systems',

            // Natural Sciences
            'Biology', 'Chemistry', 'Physics', 'Astronomy', 'Geology',

            // Mathematics & Data
            'Algebra', 'Calculus', 'Statistics', 'Data Analysis', 'Big Data',

            // Humanities & Social Sciences
            'Philosophy', 'Psychology', 'Sociology', 'Economics', 'History', 'Ethics',

            // Logic / Critical Thinking
            'Logical Fallacies', 'Argumentation', 'Epistemology', 'Critical Thinking'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate([
                'slug' => Str::slug($tagName),
            ], [
                'name' => $tagName,
            ]);
        }
    }
}
