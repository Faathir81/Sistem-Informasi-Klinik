<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokObatMutasi extends Model
{
    protected $fillable = [
        'obat_id',
        'stok_obat_id',
        'resep_detail_id',
        'pembelian_obat_detail_id',
        'tipe',
        'jumlah_masuk',
        'jumlah_keluar',
        'batch',
        'tgl_kadaluarsa',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_masuk' => 'integer',
        'jumlah_keluar' => 'integer',
        'tgl_kadaluarsa' => 'date',
    ];

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function stokObat(): BelongsTo
    {
        return $this->belongsTo(StokObat::class);
    }

    public function resepDetail(): BelongsTo
    {
        return $this->belongsTo(ResepDetail::class);
    }

    public function pembelianObatDetail(): BelongsTo
    {
        return $this->belongsTo(PembelianObatDetail::class);
    }
}
