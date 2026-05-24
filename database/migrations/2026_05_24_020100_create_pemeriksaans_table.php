<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrean_id')->unique()->constrained('antreans')->cascadeOnDelete();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->date('tgl_pemeriksaan');
            $table->text('keluhan');
            $table->text('diagnosa');
            $table->text('tindakan')->nullable();
            $table->decimal('biaya_konsultasi', 10, 2)->default(0);
            $table->enum('status_bayar', ['Belum_Bayar', 'Lunas'])->default('Belum_Bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaans');
    }
};
