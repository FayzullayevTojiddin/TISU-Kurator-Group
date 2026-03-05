<?php

namespace App\Enums;

enum DayOfWeek: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public function label(): string
    {
        return match ($this) {
            self::Monday => 'Dushanba',
            self::Tuesday => 'Seshanba',
            self::Wednesday => 'Chorshanba',
            self::Thursday => 'Payshanba',
            self::Friday => 'Juma',
            self::Saturday => 'Shanba',
            self::Sunday => 'Yakshanba',
        };
    }
}
