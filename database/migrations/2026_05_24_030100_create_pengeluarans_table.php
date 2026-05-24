<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('deskripsi');
            $table->decimal('jumlah', 10, 2);
            $table->enum('kategori', ['Operasional', 'Pembelian_Obat', 'Lain_Lain']);
            $table->date('tgl_pengeluaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
