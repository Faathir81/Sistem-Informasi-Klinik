<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gajis', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['Dokter', 'Pegawai']);
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('bulan_tahun', 7);
            $table->decimal('gaji_pokok', 10, 2);
            $table->decimal('tunjangan', 10, 2)->default(0);
            $table->decimal('potongan', 10, 2)->default(0);
            $table->decimal('total_diterima', 10, 2)->default(0);
            $table->enum('status_bayar', ['Lunas', 'Pending'])->default('Pending');
            $table->date('tgl_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gajis');
    }
};
