<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GajiHarian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['gaji_harian', 'upah_makan_harian', 'user_id'];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'gaji_harian' => 'float',
        'upah_makan_harian' => 'float',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
