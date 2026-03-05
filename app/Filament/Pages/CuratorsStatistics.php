<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\Faculty;
use App\Models\TaskSubmission;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CuratorsStatistics extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $title = 'Kuratorlar statistikasi';

    protected static ?string $navigationLabel = 'Kuratorlar';

    protected string $view = 'filament.pages.curators-statistics';

    protected static ?int $navigationSort = -1;

    public int $selectedYear;
    public int $selectedMonth;
    public ?int $selectedFacultyId = null;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isDean();
    }

    public function selectYear(int $year): void
    {
        $this->selectedYear = $year;
    }

    public function selectMonth(int $month): void
    {
        $this->selectedMonth = $month;
    }

    public function selectFaculty(?int $facultyId): void
    {
        $this->selectedFacultyId = $facultyId;
    }

    public function getMonthsProperty(): array
    {
        return [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart',
            4 => 'Aprel', 5 => 'May', 6 => 'Iyun',
            7 => 'Iyul', 8 => 'Avgust', 9 => 'Sentabr',
            10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];
    }

    public function getFacultiesProperty(): Collection
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return Faculty::where('is_active', true)->get();
        }

        if ($user->isDean()) {
            return Faculty::where('id', $user->faculty_id)->get();
        }

        return collect();
    }

    public function getCuratorsProperty(): Collection
    {
        $user = auth()->user();

        $curators = User::query()
            ->where('role', UserRole::Curator)
            ->where('is_active', true)
            ->when($this->selectedFacultyId, fn ($q) => $q->where('faculty_id', $this->selectedFacultyId))
            ->when($user->isDean(), fn ($q) => $q->where('faculty_id', $user->faculty_id))
            ->with(['faculty', 'curatedGroups'])
            ->get();

        return $curators->map(function (User $curator) {
            $groupIds = $curator->curatedGroups->pluck('id');

            if ($groupIds->isEmpty()) {
                return (object) [
                    'user' => $curator,
                    'faculty_name' => $curator->faculty?->name ?? '—',
                    'group_names' => '—',
                    'total' => 0,
                    'completed' => 0,
                    'not_completed' => 0,
                    'under_review' => 0,
                    'rejected' => 0,
                    'completion_rate' => 0,
                    'performance' => 'neutral',
                ];
            }

            $stats = TaskSubmission::query()
                ->whereIn('group_id', $groupIds)
                ->whereHas('task.week', function ($q) {
                    $q->where('year', $this->selectedYear)
                      ->where('month', $this->selectedMonth);
                })
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $completed = $stats->get('completed', 0);
            $notCompleted = $stats->get('not_completed', 0);
            $underReview = $stats->get('under_review', 0);
            $rejected = $stats->get('rejected', 0);
            $total = $completed + $notCompleted + $underReview + $rejected;

            $completionRate = $total > 0
                ? round(($completed / $total) * 100)
                : 0;

            $performance = 'neutral';
            if ($total > 0) {
                $rejectionRate = round(($rejected / $total) * 100);
                if ($completionRate >= 70) {
                    $performance = 'good';
                } elseif ($completionRate < 40 || $rejectionRate > 30) {
                    $performance = 'bad';
                }
            }

            return (object) [
                'user' => $curator,
                'faculty_name' => $curator->faculty?->name ?? '—',
                'group_names' => $curator->curatedGroups->pluck('name')->join(', '),
                'total' => $total,
                'completed' => $completed,
                'not_completed' => $notCompleted,
                'under_review' => $underReview,
                'rejected' => $rejected,
                'completion_rate' => $completionRate,
                'performance' => $performance,
            ];
        })->filter(fn ($c) => $c->total > 0)->sortByDesc('completion_rate')->values();
    }

    public function getSummaryProperty(): array
    {
        $curators = $this->curators;

        return [
            'total' => $curators->count(),
            'good' => $curators->where('performance', 'good')->count(),
            'neutral' => $curators->where('performance', 'neutral')->count(),
            'bad' => $curators->where('performance', 'bad')->count(),
        ];
    }
}
