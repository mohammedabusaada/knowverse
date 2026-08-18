<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Set when a high-reputation author pins their own discussion to the top
            // of the feed (a reputation-gated participation privilege).
            $table->timestamp('pinned_at')->nullable()->after('best_comment_id');
            $table->index('pinned_at');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['pinned_at']);
            $table->dropColumn('pinned_at');
        });
    }
};
