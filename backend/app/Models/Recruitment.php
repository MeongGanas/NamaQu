<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recruitment extends Model
{
    use HasSlug;

    public const TYPES = [
        'weekly' => 'Volunteer Mingguan',
        'event' => 'Volunteer Kegiatan Besar',
    ];

    public const METHODS = [
        'form' => 'Formulir di website',
        'instagram' => 'Balas Instagram Story',
        'external' => 'Link eksternal (Google Form, dll)',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'activity_date' => 'date',
            'registration_opens_at' => 'date',
            'registration_closes_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(VolunteerRegistration::class);
    }

    public function isOpen(): bool
    {
        $today = now()->startOfDay();

        if ($this->registration_opens_at && $today->lt($this->registration_opens_at)) {
            return false;
        }
        if ($this->registration_closes_at && $today->gt($this->registration_closes_at)) {
            return false;
        }
        if ($this->quota && $this->registrations()->where('status', '!=', 'rejected')->count() >= $this->quota) {
            return false;
        }

        return true;
    }
}
