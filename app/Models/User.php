<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function deanFaculties(): HasMany
    {
        return $this->hasMany(Faculty::class, 'dean_id');
    }

    public function getDeanFacultyIds(): array
    {
        return $this->deanFaculties()->pluck('id')->toArray();
    }

    public function curatedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'curator_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isDean(): bool
    {
        return $this->role === UserRole::Dean;
    }

    public function isCurator(): bool
    {
        return $this->role === UserRole::Curator;
    }
}
