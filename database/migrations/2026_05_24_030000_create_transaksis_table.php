<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')->unique()->constrained('pemeriksaans')->cascadeOnDelete();
            $table->string('order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['PENDING', 'SETTLEMENT', 'EXPIRE', 'CANCEL'])->default('PENDING');
            $table->string('snap_token')->nullable();
            $table->string('snap_url')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('tgl_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
