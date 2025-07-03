<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_cuti',
        'jenis_cuti',
        'tanggal_mulai_cuti',
        'tanggal_selesai_cuti',
        'foto_cuti',
        'alasan_cuti',
        'sisa_hak_cuti',
        'status',
        'alasan_persetujuan_cuti',
        'user_id',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'nama_cuti' => 'string',
        'jenis_cuti' => 'string',
        'tanggal_mulai_cuti' => 'date',
        'tanggal_selesai_cuti' => 'date',
        'alasan_cuti' => 'string',
        'foto_cuti' => 'string',
        'sisa_hak_cuti' => 'integer',
        'status' => 'string',
        'alasan_persetujuan_cuti' => 'string',
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
