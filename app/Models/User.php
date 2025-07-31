<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'email',
        'foto_face_recognition',
        'no_hp',
        'no_rekening',
        'password',
        'sisa_hak_cuti',
        'sisa_hak_cuti_bersama',
        'jabatan_id',
        'hak_cuti_id',
        'status_karyawan_id'
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'nama' => 'string',
        'email' => 'string',
        'no_hp' => 'string',
        'no_rekening' => 'string',
        'password' => 'hashed',
        'sisa_hak_cuti' => 'integer',
        'sisa_hak_cuti_bersama' => 'integer',
        'jabatan_id' => 'integer',
        'hak_cuti_id' => 'integer',
        'status_karyawan_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasPermission($permissionName)
    {
        if (!$this->jabatan) return false;

        return $this->jabatan->permissions()
            ->where('nama', $permissionName)
            ->exists();
    }

    public function boss()
    {
        $jabatan = $this->jabatan;

        if (!$jabatan) {
            return null;
        }

        // Ambil ID semua parent dari jabatan user ini
        $parentIds = $jabatan->parentJabatans()->pluck('jabatans.id');

        if ($parentIds->isEmpty()) {
            return null;
        }

        // Cari user yang jabatannya termasuk ke parent jabatan
        return User::whereIn('jabatan_id', $parentIds)->first();
    }

    public function isAdmin()
    {
        return $this->jabatan && $this->jabatan->parentJabatans()->doesntExist();
    }

    public function isBossOf(User $user)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->jabatan->childJabatans()
            ->where('jabatans.id', $user->jabatan_id) // Spesifik tabel
            ->exists();
    }

    public function isHRD()
    {
        // HRD didefinisikan sebagai user yang punya semua permission berikut
        $requiredPermissions = [
            'Tambah Penggajian',
            'Edit Penggajian',
            'Hapus Penggajian',
            'Tambah People Development',
            'Edit People Development',
            'Hapus People Development',
        ];

        return $this->jabatan
            && $this->jabatan->permissions()
            ->whereIn('nama', $requiredPermissions)
            ->count() === count($requiredPermissions);
    }

    public function subordinates()
    {
        if (!$this->jabatan) {
            return collect();
        }

        // Dapatkan semua ID jabatan bawahan (langsung dan tidak langsung)
        $allSubordinateIds = $this->getAllSubordinateJabatanIds($this->jabatan);

        return User::whereIn('jabatan_id', $allSubordinateIds);
    }

    protected function getAllSubordinateJabatanIds($jabatan, $ids = [])
    {
        // Dapatkan ID bawahan langsung
        $directSubordinates = $jabatan->childJabatans()->pluck('jabatan_hierarchys.child_jabatan_id')->toArray();

        // Gabungkan dengan yang sudah ada
        $ids = array_merge($ids, $directSubordinates);

        // Cari bawahan dari bawahan (recursive)
        foreach ($jabatan->childJabatans as $child) {
            $ids = $this->getAllSubordinateJabatanIds($child, $ids);
        }

        return array_unique($ids);
    }

    public function scopeAccessibleBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('jabatan_id', $user->jabatan_id);

            if ($user->jabatan->childJabatans()->exists()) {
                $q->orWhereIn('jabatan_id', $user->jabatan->childJabatans()->pluck('id'));
            }
        });
    }

    public function getPeopleDevelopmentsAttribute()
    {
        return $this->peopleDevelopments ?? collect();
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    public function hakCuti(): BelongsTo
    {
        return $this->belongsTo(HakCuti::class, 'hak_cuti_id', 'id');
    }

    public function statusKaryawan(): BelongsTo
    {
        return $this->belongsTo(StatusKaryawan::class, 'status_karyawan_id', 'id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'user_id', 'id');
    }

    public function absensiSales(): HasMany
    {
        return $this->hasMany(AbsensiSales::class, 'user_id', 'id');
    }

    public function cuti(): HasMany
    {
        return $this->hasMany(Cuti::class, 'user_id', 'id');
    }

    public function sakit(): HasMany
    {
        return $this->hasMany(Sakit::class, 'user_id', 'id');
    }

    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class, 'user_id', 'id');
    }

    public function peopleDevelopment(): HasMany
    {
        return $this->hasMany(PeopleDevelopment::class, 'user_id', 'id');
    }

    public function gajiBulanan(): HasOne
    {
        return $this->hasOne(GajiBulanan::class, 'user_id', 'id');
    }

    public function gajiHarian(): HasOne
    {
        return $this->hasOne(GajiHarian::class, 'user_id', 'id');
    }

    public function lembur(): HasOne
    {
        return $this->hasOne(Lembur::class, 'user_id', 'id');
    }

    public function permintaanLembur(): HasMany
    {
        return $this->hasMany(PermintaanLembur::class, 'user_id', 'id');
    }

    public function kalender(): HasMany
    {
        return $this->hasMany(Kalender::class, 'user_id', 'id');
    }

    public function isKaryawanTetap(): bool
    {
        return $this->statusKaryawan && $this->statusKaryawan->status_karyawan === 'Karyawan Tetap';
    }

    public function isKaryawanHarian(): bool
    {
        return $this->statusKaryawan && $this->statusKaryawan->status_karyawan === 'Karyawan Harian';
    }
}
