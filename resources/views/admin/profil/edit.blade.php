@extends('layouts.admin')

@section('title', 'Kelola Profil')
@section('crumb', 'KELOLA PROFIL')

@section('content')

    <div class="page-heading">
        <div class="page-heading__text">
            <h1 class="page-heading__title">Kelola profil.</h1>
            <p class="page-heading__sub">Informasi yang tampil di halaman publik Babarberan Haircuts.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="inline-error" role="alert">
            <x-icon name="circle-alert" :size="18" />
            <div>
                <p><strong>Periksa isian berikut:</strong></p>
                <ul style="margin-top:6px; padding-left:16px;">
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="form-page"
        novalidate>
        @csrf
        @method('PUT')

        {{-- 01 / Identitas usaha --}}
        <section class="form-section">
            <div class="divider"></div>
            <div class="form-section__label">
                <h2>01 / Identitas usaha</h2>
                <p>Gunakan informasi usaha yang sudah terverifikasi.</p>
            </div>

            <div class="form-row">
                <div class="field @error('nama_usaha') has-error @enderror">
                    <label class="field__label" for="nama_usaha">Nama usaha *</label>
                    <input class="field__control" id="nama_usaha" name="nama_usaha" type="text"
                        value="{{ old('nama_usaha', $profil->nama_usaha) }}">
                    @error('nama_usaha') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                </div>
                <div class="field @error('pemilik') has-error @enderror">
                    <label class="field__label" for="pemilik">Pemilik *</label>
                    <input class="field__control" id="pemilik" name="pemilik" type="text"
                        value="{{ old('pemilik', $profil->pemilik) }}">
                    @error('pemilik') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="field @error('tanggal_berdiri') has-error @enderror">
                    <label class="field__label" for="tanggal_berdiri">Tanggal berdiri *</label>
                    <input class="field__control" id="tanggal_berdiri" name="tanggal_berdiri" type="date"
                        value="{{ old('tanggal_berdiri', $profil->tanggal_berdiri?->toDateString()) }}">
                    <span class="field__help">
                        @error('tanggal_berdiri') !&nbsp;&nbsp;{{ $message }}
                        @elseif ($profil->tanggal_berdiri)
                            Ditampilkan sebagai {{ $profil->tanggal_berdiri->translatedFormat('d F Y') }}.
                            @endif
                        </span>
                    </div>

                    <div class="logo-stack">
                        @include('admin.profil._logo-box', [
                            'field' => 'logo',
                            'label' => 'Logo usaha · versi hitam (latar terang)',
                            'url' => $profil->logo_url,
                            'dark' => false,
                            'kosong' => 'Menunggu logo asli. Unggah logo setelah tersedia.',
                        ])
                        @include('admin.profil._logo-box', [
                            'field' => 'logo_putih',
                            'label' => 'Logo usaha · versi putih (latar gelap)',
                            'url' => $profil->logo_putih_url,
                            'dark' => true,
                            'kosong' => 'Dipakai di sidebar admin. Gunakan PNG/SVG transparan.',
                        ])
                    </div>
                </div>

                <div class="field @error('cerita') has-error @enderror">
                    <label class="field__label" for="cerita">Cerita usaha *</label>
                    <textarea class="field__control" id="cerita" name="cerita" rows="4">{{ old('cerita', $profil->cerita) }}</textarea>
                    @error('cerita') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                </div>
            </section>

            {{-- 02 / Kontak --}}
            <section class="form-section">
                <div class="divider"></div>
                <div class="form-section__label">
                    <h2>02 / Kontak</h2>
                    <p>Detail belum diberikan. Jangan terbitkan nomor atau akun yang belum dikonfirmasi.</p>
                </div>

                <div class="field @error('alamat') has-error @enderror">
                    <label class="field__label" for="alamat">Alamat</label>
                    <textarea class="field__control" id="alamat" name="alamat" rows="4" placeholder="Menunggu detail alamat">{{ old('alamat', $profil->alamat) }}</textarea>
                    @error('alamat') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="field @error('whatsapp') has-error @enderror">
                        <label class="field__label" for="whatsapp">WhatsApp</label>
                        <input class="field__control" id="whatsapp" name="whatsapp" type="text" inputmode="tel"
                            placeholder="Menunggu nomor WhatsApp" value="{{ old('whatsapp', $profil->whatsapp) }}">
                        @error('whatsapp') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                    </div>
                    <div class="field @error('instagram') has-error @enderror">
                        <label class="field__label" for="instagram">Instagram</label>
                        <input class="field__control" id="instagram" name="instagram" type="text"
                            placeholder="Menunggu akun Instagram" value="{{ old('instagram', $profil->instagram) }}">
                        @error('instagram') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            {{-- 03 / Jam operasional --}}
            <section class="form-section">
                <div class="divider"></div>
                <div class="form-section__label">
                    <h2>03 / Jam operasional</h2>
                    <p>Walk-in saja. Tidak ada reservasi atau pemesanan online.</p>
                </div>

                <div class="form-row">
                    <div class="field @error('hari_buka') has-error @enderror">
                        <label class="field__label" for="hari_buka">Hari buka</label>
                        <div class="select">
                            <select class="field__control" id="hari_buka" name="hari_buka">
                                @foreach ($hariBuka as $h)
                                    <option value="{{ $h }}" @selected(old('hari_buka', $profil->hari_buka) === $h)>{{ $h }}
                                    </option>
                                @endforeach
                            </select>
                            <x-icon name="chevron-down" :size="16" />
                        </div>
                        @error('hari_buka') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                    </div>

                    <div class="field @error('jam_mulai') has-error @enderror">
                        <label class="field__label" for="jam_mulai">Jam mulai</label>
                        <input class="field__control" id="jam_mulai" name="jam_mulai" type="text" inputmode="numeric"
                            placeholder="13:00" maxlength="5" value="{{ old('jam_mulai', $profil->jam_mulai) }}">
                        @error('jam_mulai') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
                    </div>

                    <div class="field @error('jam_selesai') has-error @enderror">
                        <label class="field__label" for="jam_selesai">Jam selesai</label>
                        <input class="field__control" id="jam_selesai" name="jam_selesai" type="text"
                            inputmode="numeric" placeholder="Selesai" maxlength="5"
                            value="{{ old('jam_selesai', $profil->jam_selesai) }}">
                        <span class="field__help">
                            @error('jam_selesai') !&nbsp;&nbsp;{{ $message }}
                            @else
                            Waktu tutup tidak ditentukan. @enderror
                        </span>
                    </div>
                </div>

                <label class="check-row">
                    <input type="checkbox" name="senin_tutup" value="1" @checked(session()->hasOldInput() ? old('senin_tutup') : $profil->senin_tutup)>
                    <span>Senin tutup · Ditampilkan pada informasi kunjungan.</span>
                </label>
            </section>

            <div class="form-actions">
                <x-button type="submit">Simpan perubahan</x-button>
                <x-button variant="secondary" :href="route('admin.profil.edit')">Batalkan</x-button>
            </div>
        </form>

        <div class="divider"></div>
        <p class="muted-11">Babarberan Haircuts · Ruang admin</p>
    @endsection
