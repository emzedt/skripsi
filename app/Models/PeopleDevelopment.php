<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeopleDevelopment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'periode_mulai',
        'periode_selesai',
        'jabatan',
        'keterangan'
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    /**
     * Get the user that owns the development record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all objectives for this development period
     */
    public function objectives(): HasMany
    {
        return $this->hasMany(DevelopmentObjective::class, 'development_id', 'id');
    }

    /**
     * Format the period for display
     */
    public function getPeriodeDisplayAttribute(): string
    {
        return $this->periode_mulai->format('d M Y') . ' - ' . $this->periode_selesai->format('d M Y');
    }

    /**
     * Calculate total achievement percentage
     */
    public function getTotalAchievementAttribute(): float
    {
        if ($this->objectives->isEmpty()) {
            return 0;
        }

        $total = 0;
        $totalWeight = 0;

        foreach ($this->objectives as $objective) {
            foreach ($objective->kpis as $kpi) {
                if ($kpi->bobot > 0) {
                    $total += ($kpi->realisasi / $kpi->target) * $kpi->bobot;
                    $totalWeight += $kpi->bobot;
                }
            }
        }

        return $totalWeight > 0 ? ($total / $totalWeight) * 100 : 0;
    }
}
