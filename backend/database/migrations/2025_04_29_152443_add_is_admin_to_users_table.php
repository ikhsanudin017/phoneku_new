<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('role');
        });

        // Populate is_admin based on role
        DB::table('users')
            ->where('role', 'admin')
            ->update(['is_admin' => true]);

        // Optionally, ensure non-admin roles are set to false (though default is false)
        DB::table('users')
            ->where('role', '!=', 'admin')
            ->update(['is_admin' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};