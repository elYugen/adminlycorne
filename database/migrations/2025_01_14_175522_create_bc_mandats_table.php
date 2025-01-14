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
        Schema::create('bc_mandats', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();

            $table->integer('commande_id');
            $table->foreign('commande_id')->references('id')->on('bc_commandes')->onDelete('cascade');

            $table->string('reference_unique');
            $table->string('iban');
            $table->string('bic');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bc_mandats');
    }
};
