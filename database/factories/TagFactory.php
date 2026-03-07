<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        // Generate distinct academic taxonomy terms
        $name = $this->faker->unique()->word(); 
        
        return [
            'name' => ucfirst($name),
            // Auto-generate URL-friendly routing identifiers
            'slug' => Str::slug($name),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}