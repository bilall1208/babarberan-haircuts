<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LayananController extends Controller
{
    public function index()
    {
        return $this->halaman();
    }

    public function create()
    {
        return $this->halaman(null, 'tambah');
    }

    public function edit(Layanan $layanan)
    {
        abort_unless($layanan->aktif, 404);

        return $this->halaman($layanan, 'ubah');
    }

    public function store(Request $request)
    {
        Layanan::create($this->validasi($request) + ['aktif' => true]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    public function update(Request $request, Layanan $layanan)
    {
        $layanan->update($this->validasi($request));

        return redirect()->route('admin.layanan.index')->with('success', 'Perubahan berhasil disimpan');
    }

    public function destroy(Layanan $layanan)
    {
        try {
            DB::transaction(function () use ($layanan) {
                // Sudah pernah dipakai transaksi -> disembunyikan (riwayat tetap utuh).
                // Belum pernah dipakai -> dihapus permanen.
                if ($layanan->detail()->exists()) {
                    $layanan->update(['aktif' => false]);
                } else {
                    $layanan->delete();
                }
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Layanan gagal dihapus.');
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus');
    }

    private function halaman(?Layanan $edit = null, ?string $mode = null)
    {
        $daftar = Layanan::where('aktif', true)->orderBy('id')->get();
        $jenis  = $daftar->pluck('nama')->unique()->count();

        // Catatan untuk nama layanan yang punya lebih dari satu harga
        $catatan = $daftar->groupBy('nama')
            ->filter(fn($g) => $g->count() > 1)
            ->map(function ($g) {
                $harga = $g->pluck('harga')->map(fn($h) => 'Rp' . number_format($h, 0, ',', '.'))->all();
                $teks  = implode(', ', array_slice($harga, 0, -1)) . ' dan ' . end($harga);

                return $g->first()->nama . ' menggunakan ' . count($harga) . ' entri harga: ' . $teks
                    . '. Tidak ada nama paket atau perbedaan layanan yang diasumsikan.';
            })->values();

                // Edit: tampilkan harga lain dari nama layanan yang sama
        $hargaLain = null;
        if ($edit) {
            $lain = $daftar->where('nama', $edit->nama)->where('id', '!=', $edit->id)
                ->pluck('harga')->map(fn ($h) => 'Rp' . number_format($h, 0, ',', '.'))->values()->all();

            if ($lain) {
                $hargaLain = 'Pilihan lain tersedia '
                    . implode(', ', array_slice($lain, 0, -1)) . (count($lain) > 1 ? ' dan ' : '') . end($lain) . '.';
            }
        }
        return view('admin.layanan.index', compact('daftar', 'jenis', 'catatan', 'edit', 'mode', 'hargaLain'));
    }

    private function validasi(Request $request): array
    {
        $request->merge(['harga' => preg_replace('/\D/', '', (string) $request->input('harga'))]);

        return $request->validate([
            'nama'      => ['required', 'string', 'max:60'],
            'harga'     => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string', 'max:120'],
        ], [
            'nama.required' => 'Nama layanan wajib diisi.',
            'nama.max'      => 'Nama layanan maksimal 60 karakter.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.integer'  => 'Harga harus berupa angka.',
            'harga.min'      => 'Harga harus lebih besar dari 0.',
            'deskripsi.max'  => 'Deskripsi maksimal 120 karakter.',
        ]);
    }
}
