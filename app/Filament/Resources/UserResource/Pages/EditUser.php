<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Parolni o\'zgartirish')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->form([
                    Grid::make(2)->schema([
                        TextInput::make('new_password')
                            ->label('Yangi parol')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255),

                        TextInput::make('new_password_confirmation')
                            ->label('Parolni tasdiqlash')
                            ->password()
                            ->required()
                            ->same('new_password'),
                    ]),
                ])
                ->modalHeading('Parolni o\'zgartirish')
                ->modalSubmitActionLabel('O\'zgartirish')
                ->action(function (array $data) {
                    $this->record->update([
                        'password' => Hash::make($data['new_password']),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Parol muvaffaqiyatli o\'zgartirildi')
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
