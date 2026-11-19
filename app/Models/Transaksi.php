<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = ['kode', 'tanggal', 'total', 'user_id'];
    protected $casts = ['tanggal' => 'date'];
    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
    public function getKodeTampilAttribute(): string
    {
        return 'TRX-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
