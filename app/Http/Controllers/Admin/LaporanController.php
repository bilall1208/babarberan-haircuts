<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilUsaha;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.laporan.index', $this->data($request));
    }

    public function cetak(Request $request)
    {
        return view('admin.laporan.cetak', $this->data($request) + ['profil' => ProfilUsaha::current()]);
    }

    private function data(Request $request): array
    {
        // Bawaan: 30 hari terakhir
        $dari   = $this->tanggal($request->query('dari'), now()->subDays(29));
        $sampai = $this->tanggal($request->query('sampai'), now());

        if ($dari->gt($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $layananDipilih = trim((string) $request->query('layanan', '')) ?: null;

        $query = TransaksiDetail::query()
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->whereBetween('transaksi.tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->when($layananDipilih, fn ($q) => $q->where('transaksi_detail.nama_layanan', $layananDipilih));

        $totalPendapatan = (clone $query)->sum('transaksi_detail.subtotal');
        $totalTransaksi  = (clone $query)->distinct()->count('transaksi_detail.transaksi_id');
        $totalUnit       = (clone $query)->sum('transaksi_detail.qty');

        $baris = (clone $query)
            ->select('transaksi_detail.*', 'transaksi.tanggal as tgl')
            ->orderBy('transaksi.tanggal')
            ->orderBy('transaksi_detail.id')
            ->get();

        $daftarLayanan = TransaksiDetail::query()->orderBy('nama_layanan')->distinct()->pluck('nama_layanan');

        // Catatan untuk nama layanan yang punya lebih dari satu harga
        $catatan = $baris->groupBy('nama_layanan')
            ->map(fn ($g) => $g->pluck('harga')->unique()->sort()->values())
            ->filter(fn ($h) => $h->count() > 1)
            ->map(function ($h, $nama) {
                $teks = $h->map(fn ($x) => 'Rp' . number_format($x, 0, ',', '.'))->all();

                return $nama . ' dicatat sebagai ' . count($teks) . ' pilihan harga: '
                    . implode(', ', array_slice($teks, 0, -1)) . ' dan ' . end($teks) . '.';
            })->values();

        return [
            'dari' => $dari, 'sampai' => $sampai, 'layananDipilih' => $layananDipilih,
            'totalPendapatan' => $totalPendapatan, 'totalTransaksi' => $totalTransaksi, 'totalUnit' => $totalUnit,
            'baris' => $baris, 'daftarLayanan' => $daftarLayanan, 'catatan' => $catatan,
            'periodeShort' => $this->periode($dari, $sampai, false),
            'periodeLong'  => $this->periode($dari, $sampai, true),
        ];
    }

    private function tanggal(?string $nilai, Carbon $bawaan): Carbon
    {
        try {
            return $nilai
                ? Carbon::createFromFormat('Y-m-d', $nilai)->startOfDay()
                : $bawaan->copy()->startOfDay();
        } catch (Throwable) {
            return $bawaan->copy()->startOfDay();
        }
    }

    private function periode(Carbon $dari, Carbon $sampai, bool $panjang): string
    {
        $b = $panjang ? 'F' : 'M';

        if ($dari->isSameDay($sampai)) {
            return $sampai->translatedFormat("d $b Y");
        }
        if ($dari->isSameMonth($sampai)) {
            return $dari->format('d') . '–' . $sampai->translatedFormat("d $b Y");
        }
        if ($dari->isSameYear($sampai)) {
            return $dari->translatedFormat("d $b") . ' – ' . $sampai->translatedFormat("d $b Y");
        }

        return $dari->translatedFormat("d $b Y") . ' – ' . $sampai->translatedFormat("d $b Y");
    }
}