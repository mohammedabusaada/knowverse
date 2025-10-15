<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('full_name')->nullable();
            $table->string('academic_title')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete()
                ->comment('References roles table');

            $table->text('bio')->nullable()->comment('Short user biography or description');
            $table->string('profile_picture')->nullable();

            // Cached cumulative reputation value
            $table->integer('reputation_points')
                ->default(0)
                ->comment('Cumulative sum of user reputation (synced with reputations table)');

            $table->timestamp('last_login_at')->nullable()->comment('Date and time of last login');

            $table->timestamps();
            $table->softDeletes()->comment('Soft delete support');
        });

        // Laravel default authentication support tables
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
