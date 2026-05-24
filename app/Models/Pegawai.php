<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    protected $fillable = [
        'nama_pegawai',
        'jabatan',
        'no_hp',
    ];

    public function gajis(): HasMany
    {
        return $this->hasMany(Gaji::class);
    }
}
