<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();

            // The Actor
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // The Action
            $table->string('action', 64)
                ->index()
                ->comment('Activity action identifier (e.g. post_created, vote_up)');

            // Polymorphic Target (Post, Comment, etc.)
            $table->nullableMorphs('target');

            // Metadata
            $table->text('details')
                ->nullable()
                ->comment('Additional context (title, delta, etc.)');

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            
            // Optimization 1: User-centric feed retrieval (Latest actions first)
            $table->index(['user_id', 'created_at']);

            // Optimization 2: Entity-centric timeline retrieval (Replaces default morph index)
            $table->index(['target_type', 'target_id', 'created_at'], 'target_history_timeline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};