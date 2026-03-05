<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Dean = 'dean';
    case Curator = 'curator';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Dean => 'Dekan',
            self::Curator => 'Kurator',
        };
    }
}
