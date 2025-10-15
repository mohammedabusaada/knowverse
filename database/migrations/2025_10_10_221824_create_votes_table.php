<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            // The user who cast the vote
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Polymorphic relationship: supports voting on posts or comments
            $table->morphs('target'); // Creates target_id (BIGINT) + target_type (VARCHAR) + automatic index

            // +1: upvote, -1: downvote
            $table->smallInteger('value')->comment('Vote value: +1 = upvote, -1 = downvote');

            // Timestamp when the vote was made
            $table->timestamp('created_at')->useCurrent();

            // Prevent duplicate votes by same user on the same target
            $table->unique(['user_id', 'target_type', 'target_id'], 'unique_vote_per_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
