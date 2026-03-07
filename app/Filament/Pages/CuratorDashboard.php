<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Week;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    public ?int $selectedCourse = null;
    public string $selectedStatus = 'all';

    // Kurator uchun
    public string $curatorTab = 'all';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;

        $user = auth()->user();
        if ($user->isDean()) {
            $faculty = Faculty::where('dean_id', $user->id)->first();
            $this->selectedFacultyId = $faculty?->id;
        }
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

    public function selectCourse(?int $course): void
    {
        $this->selectedCourse = $course;
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
            4 => 'Payshanba', 5 => 'Juma',
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
            return Faculty::where('dean_id', $user->id)->get();
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

    /**
     * Guruh nomining oxirgi 2 raqamidan kursni aniqlash.
     * Masalan: "25" -> 1-kurs (2025-yil qabul), "24" -> 2-kurs
     */
    protected function getCourseFromGroupName(string $groupName): ?int
    {
        if (preg_match('/(\d{2})$/', trim($groupName), $matches)) {
            $suffix = (int) $matches[1];
            $academicYearStart = $this->selectedMonth >= 9 ? $this->selectedYear : $this->selectedYear - 1;
            $course = $academicYearStart - (2000 + $suffix) + 1;
            return $course > 0 && $course <= 4 ? $course : null;
        }
        return null;
    }

    // ========== ADMIN/DEAN UCHUN ==========

    public function getAllGroupsProperty(): Collection
    {
        if (! $this->selectedWeekId) {
            return collect();
        }

        $user = auth()->user();

        $taskIds = Task::where('week_id', $this->selectedWeekId)
            ->where('day_of_week', $this->selectedDay)
            ->pluck('id');

        if ($taskIds->isEmpty()) {
            return collect();
        }

        $groups = Group::query()
            ->visibleTo($user)
            ->where('is_active', true)
            ->when($this->selectedFacultyId, fn ($q) => $q->where('faculty_id', $this->selectedFacultyId))
            ->with(['faculty', 'curator', 'submissions' => function ($q) use ($taskIds) {
                $q->whereIn('task_id', $taskIds);
            }])
            ->get();

        if ($this->selectedCourse) {
            $groups = $groups->filter(function (Group $group) {
                return $this->getCourseFromGroupName($group->name) === $this->selectedCourse;
            })->values();
        }

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

    public function exportExcel(): StreamedResponse
    {
        $groups = $this->allGroups;
        $days = $this->days;
        $dayName = $days[$this->selectedDay] ?? $this->selectedDay;
        $week = Week::find($this->selectedWeekId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Guruhlar holati');

        // Sarlavhalar
        $headers = ['#', 'Guruh', 'Fakultet', 'Kurator', 'Holat'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $statusLabels = [
            'completed' => 'Bajarildi',
            'not_completed' => 'Bajarilmadi',
            'under_review' => 'Tekshiruvda',
            'rejected' => 'Rad etildi',
        ];

        $statusColors = [
            'completed' => 'DCFCE7',
            'not_completed' => 'FEE2E2',
            'under_review' => 'DBEAFE',
            'rejected' => 'FFEDD5',
        ];

        $row = 2;
        foreach ($groups as $index => $group) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $group->name);
            $sheet->setCellValue("C{$row}", $group->faculty?->name ?? '—');
            $sheet->setCellValue("D{$row}", $group->curator?->name ?? '—');
            $sheet->setCellValue("E{$row}", $statusLabels[$group->aggregate_status] ?? $group->aggregate_status);

            $rowColor = $statusColors[$group->aggregate_status] ?? 'FFFFFF';
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row++;
        }

        $widths = [5, 25, 20, 25, 15];
        foreach ($widths as $i => $width) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($width);
        }

        $weekTitle = $week?->title ?? 'Hafta';
        $fileName = "{$weekTitle}_{$dayName}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
