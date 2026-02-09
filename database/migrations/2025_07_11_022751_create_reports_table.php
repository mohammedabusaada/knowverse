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

            // Categorized reason
            $table->string('reason_type', 50)->index(); 
            
            // Optional custom text from user
            $table->text('reason')->nullable();

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

            // Combined index for the Admin Dashboard filters
            $table->index(['reporter_id', 'status']);

            $table->unique(['reporter_id', 'target_type', 'target_id'], 'unique_report_per_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};