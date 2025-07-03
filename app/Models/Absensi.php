<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal',
        'foto_masuk',
        'jam_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'foto_keluar',
        'jam_keluar',
        'latitude_keluar',
        'longitude_keluar',
        'status',
        'lokasi_id',
        'user_id',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'tanggal' => 'date',
        'foto_masuk' => 'string',
        'jam_masuk' => 'string',
        'jam_keluar' => 'string',
        'latitude_masuk' => 'float',
        'longitude_masuk' => 'float',
        'foto_keluar' => 'string',
        'latitude_keluar' => 'float',
        'longitude_keluar' => 'float',
        'status' => 'string',
        'lokasi_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
