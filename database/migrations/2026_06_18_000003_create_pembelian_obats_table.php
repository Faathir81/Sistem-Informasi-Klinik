<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_obats', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pembelian');
            $table->string('supplier')->nullable();
            $table->decimal('total_pembelian', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pembelian_obat_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_obat_id')->constrained('pembelian_obats')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obats')->restrictOnDelete();
            $table->string('batch');
            $table->decimal('harga_beli', 10, 2)->default(0);
            $table->unsignedInteger('jumlah');
            $table->date('tgl_kadaluarsa');
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_obat_details');
        Schema::dropIfExists('pembelian_obats');
    }
};
