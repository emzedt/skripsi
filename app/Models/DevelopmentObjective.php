<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentObjective extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'development_id',
        'objective',
    ];

    /**
     * Get the development period this objective belongs to
     */
    public function development(): BelongsTo
    {
        return $this->belongsTo(PeopleDevelopment::class, 'development_id', 'id');
    }

    /**
     * Get all KPIs for this objective
     */
    public function kpis(): HasMany
    {
        return $this->hasMany(DevelopmentKpi::class, 'objective_id', 'id');
    }

    /**
     * Calculate achievement percentage for this objective
     */
    public function getAchievementAttribute(): float
    {
        if ($this->kpis->isEmpty()) {
            return 0;
        }

        $total = 0;
        $totalWeight = 0;

        foreach ($this->kpis as $kpi) {
            if ($kpi->bobot > 0) {
                $total += ($kpi->realisasi / $kpi->target) * $kpi->bobot;
                $totalWeight += $kpi->bobot;
            }
        }

        return $totalWeight > 0 ? ($total / $totalWeight) * 100 : 0;
    }
}
