<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentKpi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'objective_id',
        'kpi',
        'tipe_kpi',
        'target',
        'realisasi',
        'bobot',
        'status',
        'objective_id'
    ];

    protected $casts = [
        'target' => 'float',
        'realisasi' => 'float',
        'bobot' => 'float',
        'tipe_kpi' => 'string'
    ];

    /**
     * Get the objective this KPI belongs to
     */
    public function objective(): BelongsTo
    {
        return $this->belongsTo(DevelopmentObjective::class, 'objective_id', 'id');
    }

    /**
     * Calculate achievement percentage for this KPI
     */
    public function getAchievementAttribute(): float
    {
        if ($this->target == 0) {
            return 0;
        }
        return ($this->realisasi / $this->target) * 100;
    }

    /**
     * Automatically determine status based on achievement
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->target > 0) {
                $achievement = ($model->realisasi / $model->target) * 100;
                $model->status = $achievement >= 100 ? 'Tercapai' : 'Tidak Tercapai';
            }
        });
    }
}
