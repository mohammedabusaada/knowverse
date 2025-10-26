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

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Recipient');
            $table->foreignId('actor_id')->nullable()->constrained('users')->cascadeOnDelete()->comment('Triggering user (e.g., the user who commented)');

            $table->nullableMorphs('related_content'); // Polymorphic link to post/comment/user

            $table->enum('type', ['comment', 'vote', 'follow', 'system'])->default('system');
            $table->text('message')->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Index for fetching unread/read notifications efficiently
            $table->index(['user_id', 'is_read', 'created_at'], 'user_read_time_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
