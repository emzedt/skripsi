<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HakCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hak_cuti',
        'hak_cuti_bersama'
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'hak_cuti' => 'integer',
        'hak_cuti_bersama' => 'integer'
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }
}
