<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Models\Week;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorsStatistics extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $title = 'Kuratorlar statistikasi';

    protected static ?string $navigationLabel = 'Kuratorlar';

    protected string $view = 'filament.pages.curators-statistics';

    protected static ?int $navigationSort = -1;

    public int $selectedYear;
    public int $selectedMonth;
    public ?int $selectedWeek = null;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user->isSuperAdmin();
    }

    public function selectYear(int $year): void
    {
        $this->selectedYear = $year;
    }

    public function selectMonth(int $month): void
    {
        $this->selectedMonth = $month;
        $this->selectedWeek = null;
    }

    public function selectWeek(?int $week): void
    {
        $this->selectedWeek = $week;
    }

    public function getWeeksProperty(): Collection
    {
        return Week::query()
            ->where('year', $this->selectedYear)
            ->where('month', $this->selectedMonth)
            ->orderBy('week_number')
            ->get();
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

    public function getCuratorsProperty(): Collection
    {
        $user = auth()->user();

        $curators = User::query()
            ->where('role', UserRole::Curator)
            ->where('is_active', true)
            ->with(['curatedGroups.faculty'])
            ->get();

        return $curators->map(function (User $curator) {
            $groupIds = $curator->curatedGroups->pluck('id');

            if ($groupIds->isEmpty()) {
                return (object) [
                    'user' => $curator,
                    'faculty_name' => $curator->curatedGroups->first()?->faculty?->name ?? '—',
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
                    if ($this->selectedWeek !== null) {
                        $q->where('week_number', $this->selectedWeek);
                    }
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
                'faculty_name' => $curator->curatedGroups->first()?->faculty?->name ?? '—',
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

    public function exportExcel(): StreamedResponse
    {
        $curators = $this->curators;
        $months = $this->months;
        $monthName = $months[$this->selectedMonth] ?? $this->selectedMonth;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kuratorlar statistikasi');

        // Sarlavhalar
        $headers = ['#', 'Kurator', 'Fakultet', 'Guruh(lar)', 'Jami', 'Bajarildi', 'Bajarilmadi', 'Tekshiruvda', 'Rad etildi', 'Bajarilish %'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Sarlavha stili
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Ma'lumotlar
        $row = 2;
        foreach ($curators as $index => $curator) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $curator->user->name);
            $sheet->setCellValue("C{$row}", $curator->faculty_name);
            $sheet->setCellValue("D{$row}", $curator->group_names);
            $sheet->setCellValue("E{$row}", $curator->total);
            $sheet->setCellValue("F{$row}", $curator->completed);
            $sheet->setCellValue("G{$row}", $curator->not_completed);
            $sheet->setCellValue("H{$row}", $curator->under_review);
            $sheet->setCellValue("I{$row}", $curator->rejected);
            $sheet->setCellValue("J{$row}", $curator->completion_rate . '%');

            // Satr rangini belgilash
            $rowColor = match ($curator->performance) {
                'good' => 'DCFCE7',
                'bad' => 'FEE2E2',
                default => 'FFFFFF',
            };
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("E{$row}:J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Ustun kengliklarini sozlash
        $widths = [5, 25, 20, 30, 8, 10, 12, 12, 10, 12];
        foreach ($widths as $i => $width) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($width);
        }

        $fileName = "Kuratorlar_statistikasi_{$this->selectedYear}_{$monthName}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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
