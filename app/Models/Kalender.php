<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kalender extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['tanggal', 'keterangan', 'jenis_libur', 'user_id'];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'tanggal' => 'date',
        'keterangan' => 'string',
        'jenis_libur' => 'string',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
