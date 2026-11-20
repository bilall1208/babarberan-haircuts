# Babarberan Haircuts

Website company profile, sistem kasir, dan laporan rekap pendapatan
untuk **Babarberan Haircuts**. Proyek ini dikembangkan sebagai bagian
dari tugas Projek Kreatif Kewirausahaan (PKK) menggunakan Laravel 11 dan
MySQL.

## Tentang Proyek

Babarberan Haircuts merupakan usaha barbershop milik Nurman Taruna yang
berdiri pada 25 Desember 2020. Aplikasi ini dirancang untuk membantu
penyampaian informasi usaha sekaligus mendukung pengelolaan layanan dan
pencatatan transaksi.

## Fitur Utama

### Website Publik

-   Menampilkan profil dan informasi Babarberan Haircuts.
-   Menampilkan daftar layanan beserta harga.
-   Menampilkan galeri hasil potong rambut.
-   Menampilkan informasi kontak, lokasi, dan jam operasional.

### Halaman Admin

-   Login admin.
-   Dashboard pengelolaan.
-   Mengelola informasi profil usaha.
-   Mengelola data layanan dan harga (tambah, lihat, ubah, hapus).
-   Mencatat transaksi pelanggan.
-   Melihat dan mencetak laporan rekap pendapatan.

> Catatan: daftar fitur di atas merupakan ruang lingkup proyek.
> Sesuaikan bagian ini dengan fitur yang sudah benar-benar tersedia pada
> versi aplikasi yang sedang digunakan.

## Teknologi

-   **Framework:** Laravel 11
-   **Bahasa pemrograman:** PHP
-   **Database:** MySQL
-   **Database management:** phpMyAdmin
-   **Frontend:** Blade, HTML, CSS, dan JavaScript

## Persyaratan

Pastikan perangkat sudah memiliki: - PHP yang sesuai dengan kebutuhan
Laravel 11. - Composer. - MySQL atau MariaDB. - Node.js dan NPM (jika
menggunakan Vite atau pengelolaan aset frontend). - Git (opsional).

## Instalasi dan Menjalankan Proyek

1.  Clone repository:

    ``` bash
    git clone <URL-REPOSITORY>
    ```

2.  Masuk ke folder proyek:

    ``` bash
    cd babarberan-haircuts
    ```

3.  Install dependency PHP:

    ``` bash
    composer install
    ```

4.  Salin file konfigurasi environment:

    ``` bash
    cp .env.example .env
    ```

    Pada Windows, file `.env.example` juga dapat disalin secara manual
    menjadi `.env`.

5.  Buat database MySQL melalui phpMyAdmin, misalnya dengan nama:

    ``` text
    babarberan_haircuts
    ```

6.  Atur koneksi database pada file `.env`:

    ``` env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=babarberan_haircuts
    DB_USERNAME=root
    DB_PASSWORD=
    ```

    Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL
    di perangkat masing-masing.

7.  Generate application key:

    ``` bash
    php artisan key:generate
    ```

8.  Jalankan migration:

    ``` bash
    php artisan migrate
    ```

    Jika proyek menyediakan seeder dan ingin mengisi data awal,
    jalankan:

    ``` bash
    php artisan db:seed
    ```
9. Buat tautan public/storage:

    ``` bash
    php artisan storage:link
    ```

10.  Install dependency frontend (jika diperlukan):

    ``` bash
    npm install
    npm run build
    ```

    Untuk mode pengembangan frontend:

    ``` bash
    npm run dev
    ```

11. Jalankan server Laravel:

    ``` bash
    php artisan serve
    ```

12. Buka alamat berikut di browser:

    ``` text
    http://127.0.0.1:8000
    ```

## Struktur Direktori

Beberapa direktori penting pada Laravel:

``` text
app/
  Http/
    Controllers/    # Controller aplikasi
  Models/           # Model database

database/
  migrations/       # Struktur tabel database
  seeders/          # Data awal (jika tersedia)

public/
  css/              # File CSS publik
  images/           # Gambar publik (jika digunakan)

resources/
  views/             # Template Blade
  css/               # Aset CSS
  js/                # Aset JavaScript

routes/
  web.php            # Definisi route website

.env                 # Konfigurasi lokal (jangan diunggah)
```

Struktur aktual dapat berbeda mengikuti pengembangan aplikasi.

## Informasi Layanan

Harga layanan yang menjadi acuan informasi usaha:

  Layanan                        Harga
  ------------ -----------------------
  Dewasa                      Rp25.000
  SMP/SMA                     Rp20.000
  Anak-anak                   Rp15.000
  Cat Rambut     Rp100.000 / Rp200.000

Jam operasional: pukul 13.00 sampai selesai, Selasa--Minggu.\
Hari libur: Senin.

Pelayanan dilakukan secara langsung (*walk-in*). Website ini tidak
menyediakan pemesanan atau booking online.

## Pengembangan

Kontribusi dan pengembangan dapat dilakukan dengan membuat branch baru,
menguji perubahan, lalu mengirimkan pull request. Pastikan perubahan
database disertai migration yang sesuai.

## Keamanan dan Konfigurasi

-   Jangan membagikan file `.env` atau kredensial database.
-   Jangan mengunggah `APP_KEY`, password, atau data pribadi ke
    repository publik.
-   Gunakan konfigurasi database masing-masing saat menjalankan
    aplikasi.
-   Pastikan validasi input dan pembatasan akses admin diterapkan pada
    aplikasi.

## Tim Pengembang

Developed independently by:
- [Bilal Fahrezi]

Project: Projek Kreatif Kewirausahaan (PKK)
Major: Rekayasa Perangkat Lunak (RPL)

## Lisensi

Proyek ini dibuat untuk keperluan pembelajaran dan tugas Projek Kreatif
Kewirausahaan (PKK).
