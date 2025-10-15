<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_follows', function (Blueprint $table) {
            $table->foreignId('follower_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('followed_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('created_at')->useCurrent();

            // Composite PK: one follow per pair
            $table->primary(['follower_id', 'followed_id']);

            $table->index('followed_id'); // For "followers of user" queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_follows');
    }
};
