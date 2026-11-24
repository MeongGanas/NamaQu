<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $guarded = ['id'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    protected function casts(): array
    {
        return [
            'show_full_name' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /** Nama yang boleh ditampilkan publik, sesuai persetujuan volunteer. */
    public function getDisplayNameAttribute(): string
    {
        if ($this->show_full_name) {
            return $this->name;
        }

        return collect(explode(' ', trim($this->name)))
            ->map(fn ($part, $i) => $i === 0 ? $part : mb_substr($part, 0, 1).'.')
            ->implode(' ');
    }
}
