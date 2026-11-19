<?php

namespace Database\Seeders;

use App\Models\ProfilUsaha;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfilUsahaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfilUsaha::updateOrCreate(['id' => 1], [
            'nama_usaha'      => 'Babarberan Haircuts',
            'tagline'         => 'Tampil Lebih Rapi, Lebih Percaya Diri.',
            'pemilik'         => 'Nurman Taruna',
            'tanggal_berdiri' => '2020-12-25',
            'cerita'          => 'Babarberan Haircuts didirikan oleh Nurman Taruna pada 25 Desember 2020. Berawal dari keinginan menghadirkan tempat potong rambut yang dekat dengan keseharian, Babarberan mengutamakan hasil yang rapi, suasana santai, dan harga yang mudah dipahami.',
            'hari_buka'       => 'Selasa–Minggu',
            'jam_mulai'       => '13:00',
            'senin_tutup'     => true,
        ]);
    }
}
