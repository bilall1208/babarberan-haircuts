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
        Schema::table('profil_usaha', function (Blueprint $table) {
            $table->string('logo_putih')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('profil_usaha', function (Blueprint $table) {
            $table->dropColumn('logo_putih');
        });
    }
};
