<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasSlug;

    public const TYPES = [
        'weekly' => 'Kegiatan Mingguan',
        'event' => 'Event Khusus',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'held_on' => 'date',
            'photos' => 'array',
            'videos' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }
}
