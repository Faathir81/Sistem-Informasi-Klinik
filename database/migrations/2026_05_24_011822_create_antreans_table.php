<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antreans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->foreignId('jadwal_dokter_id')->constrained('jadwal_dokters')->cascadeOnDelete();
            $table->date('tanggal_kunjungan');
            $table->unsignedInteger('nomor_antrean');
            $table->string('kode_antrean')->unique(); // Kode unik untuk QR Code
            $table->enum('status', ['Menunggu', 'Dipanggil', 'Selesai', 'Batal'])->default('Menunggu');
            $table->timestamps();

            $table->index(['pasien_id', 'dokter_id', 'tanggal_kunjungan', 'status'], 'antreans_patient_doctor_date_status_index');
            $table->unique(['dokter_id', 'tanggal_kunjungan', 'nomor_antrean'], 'antreans_doctor_date_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antreans');
    }
};
