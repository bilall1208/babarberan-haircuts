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
            $table->string('pemilik')->nullable()->after('nama_usaha');
            $table->date('tanggal_berdiri')->nullable()->after('pemilik');
            $table->string('logo')->nullable()->after('tanggal_berdiri');
            $table->text('cerita')->nullable()->after('logo');
            $table->string('whatsapp', 30)->nullable()->after('alamat');
            $table->string('instagram', 60)->nullable()->after('whatsapp');
            $table->string('hari_buka', 30)->default('Selasa–Minggu')->after('instagram');
            $table->string('jam_mulai', 5)->nullable()->after('hari_buka');
            $table->string('jam_selesai', 5)->nullable()->after('jam_mulai');
            $table->boolean('senin_tutup')->default(true)->after('jam_selesai');

            // diganti oleh whatsapp dan hari/jam operasional
            $table->dropColumn(['telepon', 'jam_buka']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_usaha', function (Blueprint $table) {
            $table->dropColumn([
                'pemilik',
                'tanggal_berdiri',
                'logo',
                'cerita',
                'whatsapp',
                'instagram',
                'hari_buka',
                'jam_mulai',
                'jam_selesai',
                'senin_tutup'
            ]);
            $table->string('telepon', 30)->nullable();
            $table->string('jam_buka')->nullable();
        });
    }
};
