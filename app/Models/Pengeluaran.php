<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $fillable = [
        'deskripsi',
        'jumlah',
        'kategori',
        'tgl_pengeluaran',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tgl_pengeluaran' => 'date',
    ];
}
