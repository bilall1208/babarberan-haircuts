<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    protected $fillable = ['transaksi_id', 'layanan_id', 'nama_layanan', 'harga', 'qty', 'subtotal'];
    public function transaksi() { return $this->belongsTo(Transaksi::class); }
    public function layanan() { return $this->belongsTo(Layanan::class); }
}
