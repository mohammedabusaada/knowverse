<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reputations', function (Blueprint $table) {
            // Links a compensating entry to the entry it reverses. The unique index makes it
            // impossible, at the storage layer, to reverse the same ledger entry twice.
            $table->foreignId('reverses_id')
                ->nullable()
                ->after('delta')
                ->unique()
                ->constrained('reputations')
                ->cascadeOnDelete();
        });

        // Link reversals recorded before this column existed. Their note names the original
        // entry ("Reversal of ledger entry #123"). Only the first reversal of any entry is
        // linked, so historical duplicates cannot violate the new unique index.
        $linked = [];

        DB::table('reputations')
            ->where('action', 'like', '%_reverted')
            ->whereNull('reverses_id')
            ->eachById(function ($row) use (&$linked) {
                if (! preg_match('/#(\d+)/', (string) $row->note, $match)) {
                    return;
                }

                $originalId = (int) $match[1];

                if (isset($linked[$originalId]) || ! DB::table('reputations')->where('id', $originalId)->exists()) {
                    return;
                }

                DB::table('reputations')->where('id', $row->id)->update(['reverses_id' => $originalId]);
                $linked[$originalId] = true;
            });
    }

    public function down(): void
    {
        Schema::table('reputations', function (Blueprint $table) {
            $table->dropForeign(['reverses_id']);
            $table->dropUnique(['reverses_id']);
            $table->dropColumn('reverses_id');
        });
    }
};
