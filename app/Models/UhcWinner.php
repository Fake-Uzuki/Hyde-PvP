<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UhcWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'winner_gamertag',
        'runner_up_gamertag',
        'winner_kills',
        'event_date',
        'game_mode',
        'prize',
        'recap_url',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'winner_kills' => 'integer',
        ];
    }

    /**
     * Get avatar URL for the tournament champion.
     */
    public function getWinnerAvatarUrlAttribute(): string
    {
        return "https://mc-heads.net/avatar/" . rawurlencode(trim($this->winner_gamertag)) . "/64";
    }

    /**
     * Get avatar URL for the runner-up.
     */
    public function getRunnerUpAvatarUrlAttribute(): ?string
    {
        if (! $this->runner_up_gamertag) {
            return null;
        }

        return "https://mc-heads.net/avatar/" . rawurlencode(trim($this->runner_up_gamertag)) . "/48";
    }
}
