<?php

namespace App\Filament\Resources\GroupResource\Widgets;

use App\Enums\TaskStatus;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class TaskStatsWidget extends Widget
{
    public ?Model $record = null;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.resources.group-resource.widgets.task-stats-widget';

    protected function getViewData(): array
    {
        $submissions = $this->record->submissions();

        $total = $submissions->count();
        $completed = $submissions->clone()->where('status', TaskStatus::Completed)->count();
        $underReview = $submissions->clone()->where('status', TaskStatus::UnderReview)->count();
        $notCompleted = $submissions->clone()->where('status', TaskStatus::NotCompleted)->count();
        $rejected = $submissions->clone()->where('status', TaskStatus::Rejected)->count();

        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'underReview' => $underReview,
            'notCompleted' => $notCompleted,
            'rejected' => $rejected,
            'percentage' => $percentage,
        ];
    }
}
