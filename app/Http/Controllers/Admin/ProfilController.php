<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ProfilController extends Controller
{
    public const HARI_BUKA = ['Selasa–Minggu', 'Senin–Sabtu', 'Setiap hari'];

    public function edit()
    {
        $profil = ProfilUsaha::firstOrCreate(['id' => 1], ['nama_usaha' => 'Babarberan Haircuts']);

        return view('admin.profil.edit', ['profil' => $profil, 'hariBuka' => self::HARI_BUKA]);
    }

    public function update(Request $request)
    {
        $profil = ProfilUsaha::firstOrFail();

        $data = collect($request->validate([
            'nama_usaha'      => ['required', 'string', 'max:80'],
            'pemilik'         => ['required', 'string', 'max:80'],
            'tanggal_berdiri' => ['required', 'date', 'before_or_equal:today'],
            'cerita'          => ['required', 'string', 'max:1000'],
            'alamat'          => ['nullable', 'string', 'max:300'],
            'whatsapp'        => ['nullable', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'instagram'       => ['nullable', 'regex:/^@?[A-Za-z0-9._]{1,60}$/'],
            'hari_buka'       => ['required', Rule::in(self::HARI_BUKA)],
            'jam_mulai'       => ['nullable', 'date_format:H:i'],
            'jam_selesai'     => ['nullable', 'date_format:H:i'],
            'logo'            => ['nullable', 'file', 'extensions:jpg,jpeg,png,webp,svg', 'max:5120'],
            'logo_putih'      => ['nullable', 'file', 'extensions:jpg,jpeg,png,webp,svg', 'max:5120'],
        ], [
            'nama_usaha.required'             => 'Nama usaha wajib diisi.',
            'pemilik.required'                => 'Nama pemilik wajib diisi.',
            'tanggal_berdiri.required'        => 'Tanggal berdiri wajib diisi.',
            'tanggal_berdiri.date'            => 'Format tanggal tidak valid.',
            'tanggal_berdiri.before_or_equal' => 'Tanggal berdiri tidak boleh di masa depan.',
            'cerita.required'                 => 'Cerita usaha wajib diisi.',
            'cerita.max'                      => 'Cerita usaha maksimal 1000 karakter.',
            'whatsapp.regex'                  => 'Nomor WhatsApp tidak valid.',
            'instagram.regex'                 => 'Akun Instagram tidak valid.',
            'jam_mulai.date_format'           => 'Gunakan format jam seperti 13:00.',
            'jam_selesai.date_format'         => 'Gunakan format jam seperti 21:00.',
            'logo.extensions'                 => 'Logo hitam harus berformat PNG, JPG, WEBP, atau SVG.',
            'logo.max'                        => 'Logo hitam maksimal 5 MB.',
            'logo.uploaded'                   => 'Logo hitam gagal diunggah. Cek ukuran berkas.',
            'logo_putih.extensions'           => 'Logo putih harus berformat PNG, JPG, WEBP, atau SVG.',
            'logo_putih.max'                  => 'Logo putih maksimal 5 MB.',
            'logo_putih.uploaded'             => 'Logo putih gagal diunggah. Cek ukuran berkas.',
        ]))->except(['logo', 'logo_putih'])->all();

        $data['senin_tutup'] = $request->boolean('senin_tutup');

        $lama = [];
        $baru = [];

        foreach (['logo', 'logo_putih'] as $kolom) {
            if ($request->hasFile($kolom)) {
                $lama[$kolom] = $profil->{$kolom};
                $baru[$kolom] = $request->file($kolom)->store('logo', 'public');
                $data[$kolom] = $baru[$kolom];
            }
        }

        try {
            // forceFill: tidak bergantung pada $fillable di model
            DB::transaction(fn () => $profil->forceFill($data)->save());
        } catch (Throwable $e) {
            foreach ($baru as $path) {
                Storage::disk('public')->delete($path);
            }
            report($e);

            return back()->withInput()->with('error', 'Perubahan gagal disimpan, data lama tidak berubah.');
        }

        foreach ($lama as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        return redirect()->route('admin.profil.edit')->with('success', 'Profil berhasil disimpan');
    }
}