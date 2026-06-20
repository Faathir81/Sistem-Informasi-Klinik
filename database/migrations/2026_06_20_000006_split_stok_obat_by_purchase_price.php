<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_obats', function (Blueprint $table): void {
            $table->dropUnique(['obat_id', 'batch', 'tgl_kadaluarsa']);
            $table->unique(
                ['obat_id', 'batch', 'harga_beli', 'tgl_kadaluarsa'],
                'stok_obats_purchase_identity_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('stok_obats', function (Blueprint $table): void {
            $table->dropUnique('stok_obats_purchase_identity_unique');
            $table->unique(['obat_id', 'batch', 'tgl_kadaluarsa']);
        });
    }
};
