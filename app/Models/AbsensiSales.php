<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsensiSales extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal',
        'foto',
        'jam',
        'deskripsi',
        'status',
        'status_persetujuan',
        'user_id'
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'tanggal' => 'date',
        'foto' => 'string',
        'jam' => 'string',
        'deskripsi' => 'string',
        'status' => 'string',
        'status_persetujuan' => 'string',
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
