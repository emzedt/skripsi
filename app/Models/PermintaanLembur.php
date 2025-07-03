<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanLembur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['foto', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_akhir', 'lama_lembur', 'tugas', 'status', 'user_id'];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'foto' => 'string',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'lama_lembur' => 'integer',
        'tugas' => 'string',
        'status' => 'string',
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
