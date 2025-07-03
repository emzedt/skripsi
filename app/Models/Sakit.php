<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sakit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'diagnosa',
        'surat_dokter',
        'status',
        'alasan_persetujuan',
        'user_id',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'diagnosa' => 'string',
        'surat_dokter' => 'string',
        'status' => 'string',
        'alasan_persetujuan' => 'string',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
