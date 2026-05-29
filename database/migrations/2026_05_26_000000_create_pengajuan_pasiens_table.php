<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_pasiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pasien_id')->nullable()->constrained('pasiens')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nik', 16);
            $table->string('nama_pasien');
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat');
            $table->string('no_hp', 20);
            $table->text('catatan_pasien')->nullable();
            $table->string('status')->default('Menunggu Pembayaran');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['nik', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pasiens');
    }
};
