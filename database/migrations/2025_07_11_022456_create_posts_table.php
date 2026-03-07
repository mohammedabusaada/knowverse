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

            // Establish ownership with a nullable foreign key to support 'Deleted Scholar' content retention
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('body')->comment('Main content');
            $table->string('image')->nullable()->comment('Optional image path or URL');

            // Categorical state management for content lifecycle
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('published')
                ->index()
                ->comment('Post visibility status');
            
            $table->boolean('is_hidden')->default(false)->index();

            $table->unsignedBigInteger('best_comment_id')
                ->nullable()
                ->comment('Reference to the comment accepted as the Author\'s Pick');

            // Engagement and popularity metrics for ranking algorithms
            $table->integer('view_count')->default(0);
            $table->integer('upvote_count')->default(0);
            $table->integer('downvote_count')->default(0);

            $table->timestamps();
            $table->softDeletes()->comment('Soft delete');

            // Compound index to optimize author-specific feeds and title searches
            $table->index(['user_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
