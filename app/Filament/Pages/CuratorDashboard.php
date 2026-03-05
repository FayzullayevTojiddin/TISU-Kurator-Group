<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\TaskSubmission;
use App\Models\Week;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class CuratorDashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Boshqaruv paneli';

    protected static ?string $navigationLabel = 'Boshqaruv paneli';

    protected string $view = 'filament.pages.curator-dashboard';

    protected static ?int $navigationSort = -2;

    public int $selectedYear;
    public int $selectedMonth;
    public ?int $selectedWeekId = null;
    public int $selectedDay = 1;
    public ?int $selectedFacultyId = null;
    public string $selectedStatus = 'all';

    // Kurator uchun
    public string $curatorTab = 'all';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    public function selectYear(int $year): void
    {
        $this->selectedYear = $year;
        $this->selectedWeekId = null;
        $this->selectedDay = 1;
    }

    public function selectMonth(int $month): void
    {
        $this->selectedMonth = $month;
        $this->selectedWeekId = null;
        $this->selectedDay = 1;
    }

    public function selectWeek(?int $weekId): void
    {
        $this->selectedWeekId = $weekId;
        $this->selectedDay = 1;
    }

    public function selectDay(int $day): void
    {
        $this->selectedDay = $day;
    }

    public function selectFaculty(?int $facultyId): void
    {
        $this->selectedFacultyId = $facultyId;
    }

    public function selectStatus(string $status): void
    {
        $this->selectedStatus = $status;
    }

    public function selectCuratorTab(string $tab): void
    {
        $this->curatorTab = $tab;
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

    public function getDaysProperty(): array
    {
        return [
            1 => 'Dushanba', 2 => 'Seshanba', 3 => 'Chorshanba',
            4 => 'Payshanba', 5 => 'Juma', 6 => 'Shanba', 7 => 'Yakshanba',
        ];
    }

    public function getWeeksProperty(): Collection
    {
        return Week::forMonth($this->selectedYear, $this->selectedMonth)
            ->orderBy('week_number')
            ->get();
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

    // ========== KURATOR UCHUN ==========

    public function getCuratorSubmissionsProperty(): Collection
    {
        $user = auth()->user();
        $groupIds = $user->curatedGroups()->pluck('id');

        if ($groupIds->isEmpty()) {
            return collect();
        }

        return TaskSubmission::query()
            ->whereIn('group_id', $groupIds)
            ->with(['task.week', 'group', 'reviewer'])
            ->latest('updated_at')
            ->get();
    }

    public function getCuratorCountsProperty(): array
    {
        $submissions = $this->curatorSubmissions;

        return [
            'all' => $submissions->count(),
            'not_completed' => $submissions->where('status', TaskStatus::NotCompleted)->count(),
            'under_review' => $submissions->where('status', TaskStatus::UnderReview)->count(),
            'completed' => $submissions->where('status', TaskStatus::Completed)->count(),
            'rejected' => $submissions->where('status', TaskStatus::Rejected)->count(),
        ];
    }

    public function getFilteredCuratorSubmissionsProperty(): Collection
    {
        $submissions = $this->curatorSubmissions;

        if ($this->curatorTab !== 'all') {
            $submissions = $submissions->filter(
                fn ($s) => $s->status->value === $this->curatorTab
            );
        }

        return $submissions->values();
    }

    // ========== ADMIN/DEAN UCHUN ==========

    public function getAllGroupsProperty(): Collection
    {
        if (! $this->selectedWeekId) {
            return collect();
        }

        $user = auth()->user();

        $groups = Group::query()
            ->visibleTo($user)
            ->where('is_active', true)
            ->when($this->selectedFacultyId, fn ($q) => $q->where('faculty_id', $this->selectedFacultyId))
            ->with(['faculty', 'curator', 'submissions' => function ($q) {
                $q->whereHas('task', function ($tq) {
                    $tq->where('week_id', $this->selectedWeekId)
                        ->where('day_of_week', $this->selectedDay);
                });
            }])
            ->get();

        $groups->each(function (Group $group) {
            $submission = $group->submissions->first();
            $group->aggregate_status = $submission
                ? $submission->status->value
                : 'not_completed';
        });

        return $groups;
    }

    public function getStatsProperty(): array
    {
        $counts = ['completed' => 0, 'not_completed' => 0, 'under_review' => 0, 'rejected' => 0];

        foreach ($this->allGroups as $group) {
            if (isset($counts[$group->aggregate_status])) {
                $counts[$group->aggregate_status]++;
            }
        }

        return $counts;
    }

    public function getGroupsProperty(): Collection
    {
        $groups = $this->allGroups;

        if ($this->selectedStatus !== 'all') {
            $groups = $groups->filter(fn (Group $g) => $g->aggregate_status === $this->selectedStatus);
        }

        return $groups->values();
    }

    public function getStatusCountsProperty(): array
    {
        $stats = $this->stats;

        return [
            'all' => $this->allGroups->count(),
            'completed' => $stats['completed'],
            'not_completed' => $stats['not_completed'],
            'under_review' => $stats['under_review'],
            'rejected' => $stats['rejected'],
        ];
    }
}
