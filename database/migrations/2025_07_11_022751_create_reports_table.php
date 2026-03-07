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

            // Polymorphic Target (Post, Comment, etc.)
            $table->morphs('target'); 

            // Violation categorization based on ReportReason Enum
            $table->string('reason_type', 50)->index();             
            $table->text('reason')->nullable();

            // Moderation workflow state management
            $table->string('status')->default('pending')->index();

            // Admin who handled the report
            $table->foreignId('resolved_by')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin who handled the report');

            // Audit Timestamps
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Multi-column index to optimize administrative filtering
            $table->index(['reporter_id', 'status']);

            // Idempotency Constraint: Prevents a user from filing multiple active reports on the same entity
            $table->unique(['reporter_id', 'target_type', 'target_id'], 'unique_report_per_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};