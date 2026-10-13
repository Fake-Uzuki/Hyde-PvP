<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClanRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tag',
        'rank',
        'leader',
        'score',
        'wins',
        'members_count',
    ];

    protected function casts(): array
    {
        return [
            'rank' => 'integer',
            'score' => 'integer',
            'wins' => 'integer',
            'members_count' => 'integer',
        ];
    }
}
