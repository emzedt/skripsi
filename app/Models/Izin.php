<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Izin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal',
        'jenis_izin',
        'alasan',
        'dokumen_pendukung',
        'status',
        'alasan_persetujuan',
        'user_id',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'tanggal' => 'date',
        'jenis_izin' => 'string',
        'alasan' => 'string',
        'dokumen_pendukung' => 'string',
        'sisa_hak_cuti' => 'integer',
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
