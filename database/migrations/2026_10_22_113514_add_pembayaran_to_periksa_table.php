<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periksa', function (Blueprint $table) {
            // belum_bayar | menunggu_verifikasi | lunas
            $table->string('status_bayar', 25)->default('belum_bayar')->after('biaya_periksa');
            $table->string('bukti_bayar')->nullable()->after('status_bayar');
            $table->dateTime('tgl_upload_bukti')->nullable()->after('bukti_bayar');
            $table->dateTime('tgl_verifikasi')->nullable()->after('tgl_upload_bukti');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('tgl_verifikasi')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('periksa', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diverifikasi_oleh');
            $table->dropColumn(['status_bayar', 'bukti_bayar', 'tgl_upload_bukti', 'tgl_verifikasi']);
        });
    }
};
