<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    //
     protected $fillable = ['kode_transaksi', 'user_id', 'metode_bayar', 'total_bayar', 'bayar', 'kembalian'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
