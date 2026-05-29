<?php

namespace App\Services\Pasien;

use App\Models\Pasien;
use Illuminate\Support\Carbon;

class MedicalRecordNumberService
{
    public function next(): string
    {
        $prefix = 'RM-'.Carbon::now()->format('Ymd').'-';

        $last = Pasien::query()
            ->where('no_rekam_medis', 'like', $prefix.'%')
            ->orderByDesc('no_rekam_medis')
            ->lockForUpdate()
            ->first();

        $nextNumber = $last
            ? str_pad(((int) substr($last->no_rekam_medis, -4)) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return $prefix.$nextNumber;
    }
}
