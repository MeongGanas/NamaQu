<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    use HasSlug;

    public const LEVELS = [
        'kecil' => 'Tingkat Kecil',
        'sedang' => 'Tingkat Sedang',
        'besar' => 'Tingkat Besar',
    ];

    public const CATEGORIES = [
        'calistung' => 'Calistung',
        'logika_matematika' => 'Logika & Matematika',
        'game_coding' => 'Game Coding',
    ];

    public const DIFFICULTIES = [
        1 => 'Mudah',
        2 => 'Sedang',
        3 => 'Menantang',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'objectives' => 'array',
            'gallery' => 'array',
            'difficulty' => 'integer',
            'is_published' => 'boolean',
        ];
    }
}
