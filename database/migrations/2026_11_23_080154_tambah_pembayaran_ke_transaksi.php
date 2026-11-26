<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('metode_bayar', 20)->default('tunai')->after('total');
            $table->unsignedBigInteger('jumlah_bayar')->default(0)->after('metode_bayar');
            $table->unsignedBigInteger('kembalian')->default(0)->after('jumlah_bayar');
            $table->string('referensi', 50)->nullable()->after('kembalian');
        });

        // transaksi lama dianggap tunai pas
        DB::table('transaksi')->update(['jumlah_bayar' => DB::raw('total')]);
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['metode_bayar', 'jumlah_bayar', 'kembalian', 'referensi']);
        });
    }
};
