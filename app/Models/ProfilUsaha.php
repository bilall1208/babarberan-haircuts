<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilUsaha extends Model
{
    protected $table = 'profil_usaha';

    protected $fillable = [
        'nama_usaha',
        'tagline',
        'pemilik',
        'tanggal_berdiri',
        'logo',
        'logo_putih',
        'cerita',
        'alamat',
        'whatsapp',
        'instagram',
        'hari_buka',
        'jam_mulai',
        'jam_selesai',
        'senin_tutup',
    ];

    protected $casts = [
        'tanggal_berdiri' => 'date',
        'senin_tutup'     => 'boolean',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getLogoPutihUrlAttribute(): ?string
    {
        return $this->logo_putih ? asset('storage/' . $this->logo_putih) : null;
    }

    public static function current(): ?self
    {
        static $profil = false;

        if ($profil === false) {
            $profil = static::first();
        }

        return $profil;
    }

    public function getJamOperasionalAttribute(): string
    {
        $jam = $this->jam_mulai
            ? $this->jam_mulai . '–' . ($this->jam_selesai ?: 'selesai')
            : null;

        return trim($this->hari_buka . ($jam ? ' · ' . $jam : ''));
    }
}
