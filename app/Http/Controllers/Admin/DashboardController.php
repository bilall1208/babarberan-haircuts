<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class DashboardController extends Controller
{
    public function index()
    {
        // Periode ringkasan: 7 hari terakhir (nanti bisa diganti filter di Laporan)
        $akhir = now()->startOfDay();
        $awal  = now()->subDays(6)->startOfDay();

        $periode = Transaksi::whereBetween('tanggal', [$awal->toDateString(), $akhir->toDateString()]);

        $pendapatanPeriode = (clone $periode)->sum('total');
        $jumlahTransaksi   = (clone $periode)->count();

        $unitLayanan = TransaksiDetail::whereHas(
            'transaksi',
            fn($q) =>
            $q->whereBetween('tanggal', [$awal->toDateString(), $akhir->toDateString()])
        )->sum('qty');

        $hariIni = Transaksi::whereDate('tanggal', now()->toDateString());
        $pendapatanHariIni = (clone $hariIni)->sum('total');
        $transaksiHariIni  = (clone $hariIni)->count();

        $harian = Transaksi::whereBetween('tanggal', [$awal->toDateString(), $akhir->toDateString()])
            ->selectRaw('tanggal, SUM(total) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        $maks = max((int) $harian->max('total'), 1);

        $terbaru    = TransaksiDetail::with('transaksi')->latest('id')->limit(4)->get();
        $totalBaris = TransaksiDetail::count();

        return view('admin.dashboard', compact(
            'awal',
            'akhir',
            'pendapatanPeriode',
            'jumlahTransaksi',
            'unitLayanan',
            'pendapatanHariIni',
            'transaksiHariIni',
            'harian',
            'maks',
            'terbaru',
            'totalBaris'
        ));
    }
}
