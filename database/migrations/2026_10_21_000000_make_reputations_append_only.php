<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // The reputation ledger is append-only: a correction is a new compensating entry, never an edit.
    // These triggers enforce that rule in the database itself, so a direct UPDATE or DELETE is rejected
    // even when it bypasses the application. Permanent account deletion remains possible: the user's
    // entries are removed by the foreign-key cascade, which does not fire triggers in MySQL/MariaDB.
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::unprepared("CREATE TRIGGER reputations_no_update BEFORE UPDATE ON reputations FOR EACH ROW
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The reputation ledger is append-only'");
            DB::unprepared("CREATE TRIGGER reputations_no_delete BEFORE DELETE ON reputations FOR EACH ROW
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The reputation ledger is append-only'");
        } elseif ($driver === 'sqlite') {
            // SQLite fires triggers on cascaded deletes, so a delete is allowed only once the owning
            // user is gone, which is the case only during permanent account deletion.
            DB::unprepared("CREATE TRIGGER reputations_no_update BEFORE UPDATE ON reputations
                BEGIN SELECT RAISE(ABORT, 'The reputation ledger is append-only'); END");
            DB::unprepared("CREATE TRIGGER reputations_no_delete BEFORE DELETE ON reputations
                WHEN EXISTS (SELECT 1 FROM users WHERE id = OLD.user_id)
                BEGIN SELECT RAISE(ABORT, 'The reputation ledger is append-only'); END");
        }
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS reputations_no_update');
        DB::unprepared('DROP TRIGGER IF EXISTS reputations_no_delete');
    }
};
