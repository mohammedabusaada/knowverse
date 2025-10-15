<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity', function (Blueprint $table) {
            $table->id();

            // User performing the action
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // The type of action
            $table->enum('action', [
                'post', 'comment', 'vote', 'follow',
                'report', 'login', 'logout'
            ])->comment('Type of user action');

            // Polymorphic target (what the action was performed on)
            $table->nullableMorphs('target'); // Entity the action was performed on

            // Optional details (e.g., post title or short description)
            $table->text('details')->nullable()->comment('Additional context about the activity');

            $table->timestamp('created_at')->useCurrent();

            // --- Indexes ---
            $table->index(['user_id', 'created_at']); // activity feed
            $table->index('action');                  // analytics
            // index(target_type, target_id) auto-created by nullableMorphs()
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity');
    }
};
