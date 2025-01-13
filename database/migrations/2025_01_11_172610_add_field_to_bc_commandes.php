<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bc_commandes', function (Blueprint $table) {
            $table->string('payment_token')->nullable();
            $table->timestamp('payment_link_expires_at')->nullable();
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
