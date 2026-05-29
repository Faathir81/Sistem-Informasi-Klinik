<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasIndex('antreans', 'antreans_pasien_id_index')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->index('pasien_id', 'antreans_pasien_id_index');
            });
        }

        if (! Schema::hasIndex('antreans', 'antreans_dokter_id_index')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->index('dokter_id', 'antreans_dokter_id_index');
            });
        }

        if (Schema::hasIndex('antreans', 'antreans_pasien_id_dokter_id_tanggal_kunjungan_unique', 'unique')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->dropUnique(['pasien_id', 'dokter_id', 'tanggal_kunjungan']);
            });
        }

        if (! Schema::hasIndex('antreans', 'antreans_patient_doctor_date_status_index')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->index(['pasien_id', 'dokter_id', 'tanggal_kunjungan', 'status'], 'antreans_patient_doctor_date_status_index');
            });
        }

        if (! Schema::hasIndex('antreans', 'antreans_doctor_date_number_unique', 'unique')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->unique(['dokter_id', 'tanggal_kunjungan', 'nomor_antrean'], 'antreans_doctor_date_number_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('antreans', 'antreans_doctor_date_number_unique', 'unique')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->dropUnique('antreans_doctor_date_number_unique');
            });
        }

        if (Schema::hasIndex('antreans', 'antreans_patient_doctor_date_status_index')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->dropIndex('antreans_patient_doctor_date_status_index');
            });
        }

        if (! Schema::hasIndex('antreans', 'antreans_pasien_id_dokter_id_tanggal_kunjungan_unique', 'unique')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->unique(['pasien_id', 'dokter_id', 'tanggal_kunjungan']);
            });
        }

        if (Schema::hasIndex('antreans', 'antreans_dokter_id_index')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->dropIndex('antreans_dokter_id_index');
            });
        }

        if (Schema::hasIndex('antreans', 'antreans_pasien_id_index')) {
            Schema::table('antreans', function (Blueprint $table) {
                $table->dropIndex('antreans_pasien_id_index');
            });
        }
    }
};
