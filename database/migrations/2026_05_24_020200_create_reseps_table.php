<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')->unique()->constrained('pemeriksaans')->cascadeOnDelete();
            $table->decimal('total_harga_obat', 10, 2)->default(0);
            $table->enum('status_ambil', ['Belum_Diambil', 'Sudah_Diambil'])->default('Belum_Diambil');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
