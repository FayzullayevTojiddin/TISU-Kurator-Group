<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Completed = 'completed';
    case NotCompleted = 'not_completed';
    case UnderReview = 'under_review';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Bajarildi',
            self::NotCompleted => 'Bajarilmadi',
            self::UnderReview => 'Tekshiruvda',
            self::Rejected => 'Rad etildi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Completed => 'success',
            self::NotCompleted => 'danger',
            self::UnderReview => 'warning',
            self::Rejected => 'gray',
        };
    }
}
