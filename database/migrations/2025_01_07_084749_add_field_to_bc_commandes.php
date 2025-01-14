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
        Schema::table('bc_commandes', function (Blueprint $table) {
            $table->boolean('isProcessed');
            $table->boolean('is_cgv_validated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bc_commandes', function (Blueprint $table) {
            //
        });
    }
};
