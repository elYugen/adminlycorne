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
            $table->integer('phone_number');
            $table->string('entreprise');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bc_utilisateurs', function (Blueprint $table) {
            //
        });
    }
};
