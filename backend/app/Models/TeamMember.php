<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $guarded = ['id'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    protected function casts(): array
    {
        return [
            'is_founder' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
