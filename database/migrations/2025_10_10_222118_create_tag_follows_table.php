<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_follows', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->constrained('tags')
                ->cascadeOnDelete();

            $table->timestamp('created_at')->useCurrent();

            $table->primary(['user_id', 'tag_id']);

            $table->index('tag_id'); // For "followers of tag" queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_follows');
    }
};
