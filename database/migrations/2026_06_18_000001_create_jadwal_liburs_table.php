<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_liburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->unique(['dokter_id', 'tanggal']);
            $table->index(['tanggal', 'status_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_liburs');
    }
};
