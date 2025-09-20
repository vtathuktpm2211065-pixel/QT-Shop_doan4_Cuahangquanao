<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            if (!Schema::hasColumn('team_members', 'password')) {
                $table->string('password')->after('email');
            }
            if (!Schema::hasColumn('team_members', 'role')) {
                $table->string('role')->default('staff')->after('password');
            }
            if (!Schema::hasColumn('team_members', 'photo')) {
                $table->string('photo')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('team_members', 'permissions')) {
                $table->json('permissions')->nullable()->after('photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $columns = ['password', 'role', 'photo', 'permissions'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('team_members', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
