<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class GajiSlipController extends Controller
{
    public function __invoke(Gaji $gaji): Response
    {
        $gaji->load(['dokter', 'pegawai']);

        return Pdf::loadView('gaji.slip', compact('gaji'))
            ->setPaper('a4')
            ->download('slip-gaji-'.$gaji->id.'.pdf');
    }
}
