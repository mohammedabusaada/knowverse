<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
    $table->id();

    // Recipient
    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    // Actor (who triggered it)
    $table->foreignId('actor_id')
        ->nullable()
        ->constrained('users')
        ->cascadeOnDelete();

    // Target entity (post, comment, user, etc.)
    $table->nullableMorphs('target');

    // Event-based type identifier
    $table->string('type', 64)
        ->index()
        ->comment('Event identifier (e.g. post_commented, vote_up)');

    // Optional presentation text (fallback)
    $table->text('message')->nullable();

    // Read state
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();

    $table->timestamps();

    // Feed optimization
    $table->index(['user_id', 'is_read', 'created_at']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
