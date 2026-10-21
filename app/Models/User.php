<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string> $fillable
     */
    protected $fillable = [
        'nama',
        'alamat',
        'no_ktp',
        'no_hp',
        'no_rm',
        'role',
        'id_poli',
        'email',
        'password'
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli');
    }

    public function jadwalPeriksas()
    {
        return $this->hasMany(JadwalPeriksa::class, 'id_dokter');
    }

    /**
     * Buat nomor rekam medis otomatis dengan format TAHUNBULAN-URUTAN,
     * contoh: 202610-001 (pasien pertama yang daftar di Oktober 2026).
     * Urutan dimulai lagi dari 001 setiap bulan.
     */
    public static function generateNoRm($tanggal = null): string
    {
        $prefix = ($tanggal ? Carbon::parse($tanggal) : now())->format('Ym');

        // Ambil angka urutan terbesar bulan tersebut (bagian setelah "202610-")
        $terakhir = static::where('role', 'pasien')
            ->where('no_rm', 'like', $prefix . '-%')
            ->selectRaw('MAX(CAST(SUBSTRING(no_rm, 8) AS UNSIGNED)) as urutan')
            ->value('urutan');

        return $prefix . '-' . str_pad(((int) $terakhir) + 1, 3, '0', STR_PAD_LEFT);
    }
}