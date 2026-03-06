<?php

namespace App\Filament\Resources\TaskSubmissionResource\Pages;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskSubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskSubmission extends EditRecord
{
    protected static string $resource = TaskSubmissionResource::class;

    public function getTitle(): string
    {
        return $this->record->task?->title ?? 'Topshirish';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()?->isCurator()) {
            $data['status'] = TaskStatus::UnderReview;
            $data['submitted_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canReview = $user->isSuperAdmin() || $user->isDean();

        return [
            Action::make('approve')
                ->label('Tasdiqlash')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Topshiriqni tasdiqlash')
                ->modalDescription('Ushbu topshiriqni tasdiqlashni xohlaysizmi?')
                ->modalSubmitActionLabel('Tasdiqlash')
                ->visible(fn () => $canReview && $this->record->status !== TaskStatus::Completed)
                ->action(function () {
                    $this->record->update([
                        'status' => TaskStatus::Completed,
                        'reviewer_id' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'reviewer_id', 'reviewed_at']);
                }),

            Action::make('reject')
                ->label('Bekor qilish')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Tasdiqlashni bekor qilish')
                ->modalDescription('Ushbu topshiriq tasdiqlashini bekor qilishni xohlaysizmi?')
                ->modalSubmitActionLabel('Bekor qilish')
                ->visible(fn () => $canReview && $this->record->status === TaskStatus::Completed)
                ->action(function () {
                    $this->record->update([
                        'status' => TaskStatus::NotCompleted,
                        'reviewer_id' => null,
                        'reviewed_at' => null,
                    ]);
                    $this->refreshFormData(['status', 'reviewer_id', 'reviewed_at']);
                }),

            DeleteAction::make()
                ->visible($canReview),
        ];
    }

    protected function getRedirectUrl(): string
    {
        if (auth()->user()?->isCurator()) {
            return route('filament.admin.pages.curator-dashboard');
        }

        return $this->getResource()::getUrl('index');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->url(fn () => auth()->user()?->isCurator()
                ? route('filament.admin.pages.curator-dashboard')
                : $this->getResource()::getUrl('index')
            );
    }
}
