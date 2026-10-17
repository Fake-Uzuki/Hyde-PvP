<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'gamertag',
        'rank',
        'rating',
        'wins',
        'losses',
        'kills',
        'deaths',
        'clan',
    ];

    protected function casts(): array
    {
        return [
            'rank' => 'integer',
            'rating' => 'integer',
            'wins' => 'integer',
            'losses' => 'integer',
            'kills' => 'integer',
            'deaths' => 'integer',
        ];
    }

    public function getKdRatioAttribute(): float
    {
        if ($this->deaths === 0) {
            return (float) $this->kills;
        }

        return round($this->kills / $this->deaths, 2);
    }

    public function getPointsAttribute(): int
    {
        return (int) $this->rating;
    }
}
