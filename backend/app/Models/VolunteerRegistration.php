<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerRegistration extends Model
{
    public const STATUSES = [
        'pending' => 'Menunggu',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
    ];

    protected $guarded = ['id'];

    public function recruitment(): BelongsTo
    {
        return $this->belongsTo(Recruitment::class);
    }
}
