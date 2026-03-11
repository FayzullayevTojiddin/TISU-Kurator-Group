<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Shaxsiy ma\'lumotlar')
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label('Ism')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Parol')
                        ->password()
                        ->required()
                        ->maxLength(255)
                        ->visible(fn (string $operation): bool => $operation === 'create'),

                    Select::make('role')
                        ->label('Rol')
                        ->options(function () {
                            $user = auth()->user();

                            if ($user->isDean()) {
                                return [UserRole::Curator->value => UserRole::Curator->label()];
                            }

                            return collect(UserRole::cases())->mapWithKeys(
                                fn (UserRole $role) => [$role->value => $role->label()]
                            );
                        })
                        ->default(fn () => auth()->user()->isDean() ? UserRole::Curator->value : null)
                        ->disabled(fn () => auth()->user()->isDean())
                        ->dehydrated()
                        ->required()
                        ->native(false),

                ]),
        ]);
    }
}
