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
            $table->dropColumn('password');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
            $table->dropColumn('phone_number');
            
            $table->string('phone_number')->after('email');
            $table->string('lastname')->after('name');
            $table->string('civilite')->after('phone_number');
            $table->string('address')->after('civilite');
            $table->string('postal_code')->after('address');
            $table->string('siret')->after('entreprise');

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
