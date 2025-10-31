<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('body')->comment('Main content');
            $table->string('image')->nullable()->comment('Optional image path or URL');

            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('published')
                ->comment('Post visibility status');

            $table->unsignedBigInteger('best_comment_id')
                ->nullable()
                ->comment('Optional: best comment chosen by post author');

            $table->integer('view_count')->default(0);
            $table->integer('upvote_count')->default(0);
            $table->integer('downvote_count')->default(0);

            $table->timestamps();
            $table->softDeletes()->comment('Soft delete');

            // Index for efficient filtering by user or title searches
            $table->index(['user_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
