<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama'];

    public $incrementing = true;
    protected $keyType = 'int';

    public $casts = [
        'nama' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'jabatan_id', 'id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_jabatans');
    }

    public function parentJabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'jabatan_hierarchys', 'child_jabatan_id', 'parent_jabatan_id');
    }

    public function childJabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'jabatan_hierarchys', 'parent_jabatan_id', 'child_jabatan_id');
    }

    public function getAllPermissionsAttribute()
    {
        return $this->permissions->groupBy('group');
    }
}
