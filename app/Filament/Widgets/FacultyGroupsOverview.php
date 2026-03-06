<?php

namespace App\Filament\Widgets;

use App\Models\Faculty;
use App\Models\Group;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FacultyGroupsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $user = auth()->user();

        $faculties = match (true) {
            $user->isSuperAdmin() => Faculty::where('is_active', true)->withCount('groups')->get(),
            $user->isDean() => Faculty::where('dean_id', $user->id)->withCount('groups')->get(),
            default => collect(),
        };

        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats[] = Stat::make('Jami guruhlar', Group::count())
                ->icon('heroicon-o-user-group')
                ->color('primary');
        }

        foreach ($faculties as $faculty) {
            $stats[] = Stat::make($faculty->name, $faculty->groups_count)
                ->icon('heroicon-o-academic-cap')
                ->color('success');
        }

        return $stats;
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isDean();
    }
}
