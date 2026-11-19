<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\ProfilUsaha;

class PublicController extends Controller
{
    public function home()
    {
        $profil = ProfilUsaha::current();

        $rp   = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');
        $kata = [2 => 'Dua', 3 => 'Tiga', 4 => 'Empat'];

        $grup = Layanan::where('aktif', true)->orderBy('id')->get()->groupBy('nama');

        // Nama yang sama dengan beberapa harga digabung jadi satu baris (mis. Cat Rambut)
        $layanan = $grup->map(function ($g, $nama) use ($rp, $kata) {
            $harga = $g->pluck('harga')->unique()->sort()->values();

            $deskripsi = $g->pluck('deskripsi')->filter()->first()
                ?? ($harga->count() > 1
                    ? ($kata[$harga->count()] ?? 'Beberapa') . ' pilihan harga. Tanyakan langsung di tempat.'
                    : null);

            return [
                'nama'      => $nama,
                'deskripsi' => $deskripsi,
                'harga'     => $harga->map($rp)->implode(' / '),
                'ganda'     => $harga->count() > 1,
                'daftar'    => $harga->map($rp)->all(),
            ];
        })->values();

        $kalimat = $layanan->filter(fn ($l) => $l['ganda'])->map(function ($l) {
            $h = $l['daftar'];

            return 'Harga ' . $l['nama'] . ' tersedia ' . implode(', ', array_slice($h, 0, -1)) . ' dan ' . end($h) . '.';
        });

        $catatanHarga = $kalimat->isNotEmpty()
            ? $kalimat->implode(' ') . ' Detail pilihan dapat ditanyakan saat berkunjung.'
            : null;

        $jamKunjungan = $profil?->jam_mulai
            ? $profil->hari_buka . ', mulai ' . $profil->jam_mulai . ' hingga ' . ($profil->jam_selesai ?: 'selesai') . '.'
            : 'Selasa–Minggu, mulai 13:00 hingga selesai.';

                // Jawaban FAQ jam buka, dari Kelola Profil
        $hariBuka = str_replace('–', ' sampai ', $profil?->hari_buka ?: 'Selasa–Minggu');
        $jamBuka  = $profil?->jam_mulai
            ? 'pukul ' . $profil->jam_mulai . '–' . ($profil->jam_selesai ?: 'selesai')
            : 'jam buka segera diumumkan';
        $faqJam = $hariBuka . ', ' . $jamBuka . '.' . (($profil?->senin_tutup ?? true) ? ' Senin tutup.' : '');

        // Tautan kontak (hanya jika sudah diisi)
        $wa = $profil?->whatsapp ? preg_replace('/\D/', '', $profil->whatsapp) : null;
        if ($wa && str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }
        $waUrl = $wa ? 'https://wa.me/' . $wa : null;

        $ig    = $profil?->instagram ? ltrim($profil->instagram, '@') : null;
        $igUrl = $ig ? 'https://instagram.com/' . $ig : null;
        return view('public.home', compact(
            'profil', 'layanan', 'catatanHarga', 'jamKunjungan', 'faqJam', 'waUrl', 'igUrl'
        ));
    }
}