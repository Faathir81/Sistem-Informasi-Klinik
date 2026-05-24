<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = [
        'nama_pegawai',
        'jabatan',
        'no_hp',
    ];
}
