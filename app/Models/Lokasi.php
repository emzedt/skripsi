<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'latitude', 'longitude', 'radius'];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'nama' => 'string',
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'absensi_id', 'id');
    }
}
