<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksis', 'pengajuan_pasien_id')) {
                $table->foreignId('pengajuan_pasien_id')
                    ->nullable()
                    ->after('pemeriksaan_id')
                    ->unique()
                    ->constrained('pengajuan_pasiens')
                    ->cascadeOnDelete();
            }
        });

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE transaksis MODIFY pemeriksaan_id BIGINT UNSIGNED NULL');
            DB::statement("ALTER TABLE pengajuan_pasiens MODIFY status VARCHAR(255) NOT NULL DEFAULT 'Menunggu Pembayaran'");
        } else {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->foreignId('pemeriksaan_id')->nullable()->change();
            });

            Schema::table('pengajuan_pasiens', function (Blueprint $table) {
                $table->string('status')->default('Menunggu Pembayaran')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'pengajuan_pasien_id')) {
                $table->dropConstrainedForeignId('pengajuan_pasien_id');
            }
        });
    }
};
