<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bc_utilisateurs', function (Blueprint $table) {
            $table->string('role')->default('revendeur')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bc_utilisateurs', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
