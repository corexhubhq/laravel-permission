<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Adds system_id (when use_system_scope is true) and the direct permissions JSON
 * column (when user_permissions_column is configured) to the users table.
 *
 * Skip this migration if your users table already has these columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $permissionsColumn = config('permission.user_permissions_column');
            if ($permissionsColumn && !Schema::hasColumn('users', $permissionsColumn)) {
                $table->json($permissionsColumn)->nullable()->after('id');
            }

            if (config('permission.use_system_scope') && !Schema::hasColumn('users', 'system_id')) {
                $table->unsignedBigInteger('system_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $permissionsColumn = config('permission.user_permissions_column');
            if ($permissionsColumn && Schema::hasColumn('users', $permissionsColumn)) {
                $table->dropColumn($permissionsColumn);
            }

            if (config('permission.use_system_scope') && Schema::hasColumn('users', 'system_id')) {
                $table->dropColumn('system_id');
            }
        });
    }
};
