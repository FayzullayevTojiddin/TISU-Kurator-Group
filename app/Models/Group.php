<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'faculty_id',
        'curator_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role->value) {
            'super_admin' => $query,
            'dean' => $query->whereIn('faculty_id', Faculty::where('dean_id', $user->id)->pluck('id')),
            'curator' => $query->where('curator_id', $user->id),
        };
    }
}
