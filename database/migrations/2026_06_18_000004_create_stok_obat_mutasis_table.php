<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_obat_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obats')->restrictOnDelete();
            $table->foreignId('stok_obat_id')->nullable()->constrained('stok_obats')->nullOnDelete();
            $table->foreignId('resep_detail_id')->nullable()->constrained('resep_details')->nullOnDelete();
            $table->foreignId('pembelian_obat_detail_id')->nullable()->constrained('pembelian_obat_details')->nullOnDelete();
            $table->string('tipe');
            $table->unsignedInteger('jumlah_masuk')->default(0);
            $table->unsignedInteger('jumlah_keluar')->default(0);
            $table->string('batch')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['obat_id', 'tipe']);
            $table->index(['stok_obat_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_obat_mutasis');
    }
};
