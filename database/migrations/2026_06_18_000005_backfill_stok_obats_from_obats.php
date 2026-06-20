<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stok_obats') || ! Schema::hasTable('obats')) {
            return;
        }

        DB::table('obats')
            ->where('stok', '>', 0)
            ->orderBy('id')
            ->get()
            ->each(function ($obat): void {
                $expired = $obat->tgl_kadaluarsa ?: now()->addYear()->toDateString();
                $batch = 'AWAL-'.$obat->id.'-'.Str::of($expired)->replace('-', '')->toString();

                DB::table('stok_obats')->updateOrInsert(
                    [
                        'obat_id' => $obat->id,
                        'batch' => $batch,
                        'tgl_kadaluarsa' => $expired,
                    ],
                    [
                        'harga_beli' => $obat->harga_beli ?? 0,
                        'stok' => $obat->stok,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            });
    }

    public function down(): void
    {
        DB::table('stok_obats')
            ->where('batch', 'like', 'AWAL-%')
            ->delete();
    }
};
