<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periksa extends Model
{
    public const STATUS_BELUM = 'belum_bayar';
    public const STATUS_MENUNGGU = 'menunggu_verifikasi';
    public const STATUS_LUNAS = 'lunas';

    protected $table = 'periksa';

    protected $fillable = [
        'id_daftar_poli',
        'tgl_periksa',
        'catatan',
        'biaya_periksa',
        'status_bayar',
        'bukti_bayar',
        'tgl_upload_bukti',
        'tgl_verifikasi',
        'diverifikasi_oleh',
    ];

    protected $casts = [
        'tgl_periksa'      => 'datetime',
        'tgl_upload_bukti' => 'datetime',
        'tgl_verifikasi'   => 'datetime',
    ];

    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_periksa');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
