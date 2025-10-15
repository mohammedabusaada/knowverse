<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Reporter
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();

            // Polymorphic Target (what is being reported)
            $table->morphs('target'); // Reported entity (post, comment, or user)

            // Report Details
            $table->text('reason')->nullable();

            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');

            // Reviewer (Admin who handled the report)
            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin who reviewed the report');

            // Timestamps (Using both for auditing the status change)
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();


            // Index: For quickly finding reports by the reporter and status
            $table->index(['reporter_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
