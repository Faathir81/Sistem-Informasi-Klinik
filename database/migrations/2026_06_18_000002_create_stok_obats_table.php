<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obats')->restrictOnDelete();
            $table->string('batch');
            $table->decimal('harga_beli', 10, 2)->default(0);
            $table->unsignedInteger('stok')->default(0);
            $table->date('tgl_kadaluarsa');
            $table->timestamps();

            $table->unique(['obat_id', 'batch', 'tgl_kadaluarsa']);
            $table->index(['obat_id', 'tgl_kadaluarsa']);
            $table->index(['tgl_kadaluarsa', 'stok']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_obats');
    }
};
