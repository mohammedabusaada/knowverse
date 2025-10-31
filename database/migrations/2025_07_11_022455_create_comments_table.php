<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedBigInteger('parent_id')->nullable()->comment('Self-reference for replies');

            $table->text('body');
            $table->integer('upvote_count')->default(0);
            $table->integer('downvote_count')->default(0);

            $table->timestamps();
            $table->softDeletes()->comment('Soft delete');

            $table->index(['post_id', 'user_id']);
            $table->index('parent_id'); // For threaded comment queries

            $table->foreign('parent_id')->references('id')->on('comments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
