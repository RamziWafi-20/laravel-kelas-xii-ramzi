<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user');
        });

        if (Schema::hasTable('roles') && Schema::hasColumn('users', 'role_id')) {
            foreach (DB::table('roles')->get(['id', 'name']) as $role) {
                DB::table('users')
                    ->where('role_id', $role->id)
                    ->update(['role' => $role->name]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
