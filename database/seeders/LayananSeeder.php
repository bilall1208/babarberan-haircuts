<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Dewasa',     25000,  'Potongan untuk keseharianmu.'],
            ['SMP/SMA',    20000,  'Rapi untuk sekolah dan setelahnya.'],
            ['Anak-anak',  15000,  'Tampilan rapi untuk si kecil.'],
            ['Cat Rambut Biasa', 100000, 'Untuk penjelasan lebih lanjut. Tanyakan langsung di tempat.'],
            ['Cat Rambut Premium', 200000, 'Untuk penjelasan lebih lanjut. Tanyakan langsung di tempat.'],
        ];

        foreach ($data as [$nama, $harga, $deskripsi]) {
            Layanan::updateOrCreate(
                ['nama' => $nama, 'harga' => $harga],
                ['deskripsi' => $deskripsi, 'aktif' => true]
            );
        }
    }
}