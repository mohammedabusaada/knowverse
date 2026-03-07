<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reputations', function (Blueprint $table) {
            $table->id();

            // User receiving the reputation change
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('action')
                ->comment('Triggering event (e.g., post_upvote, comment_accepted)');

            // Delta: how much the reputation changed (+ or -) (impact on user standing)
            $table->integer('delta')
                ->comment('Change in reputation points (e.g., +10, -2)');

            // Polymorphic source of reputation change (post, comment, etc.)
            $table->nullableMorphs('source');

            $table->text('note')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Query optimization for historical reputation timeline rendering
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reputations');
    }
};
