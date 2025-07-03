<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penggajian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'periode_mulai',
        'periode_selesai',
        'gaji_diterima',
        'lembur',
        'potongan_gaji'
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'user_id' => 'integer',
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'gaji_diterima' => 'float',
        'lembur' => 'float',
        'potongan_gaji' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
