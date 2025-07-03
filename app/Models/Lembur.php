<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lembur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['upah_lembur_per_jam', 'upah_lembur_over_5_jam', 'user_id'];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'upah_lembur_per_jam' => 'integer',
        'upah_lembur_over_5_jam' => 'integer',
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
