<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;

use Filament\Actions\Action;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rules\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            Action::make('changePassword')->label('Ubah Password')
                ->form([
                    TextInput::make('new_password')
                        ->label('Password Baru')
                        ->password()
                        ->required()
                        ->rule(Password::default()),
                    TextInput::make('new_password_confirmation')
                        ->label('confirmasi Password Baru')
                        ->password()
                        ->required()
                        ->same('new_password')
                        ->rule(Password::default()),

                ])->action(function (array $data) {
                    $this->record->update([
                        'password' => bcrypt($data['new_password'])
                    ]);
                    Notification::make()
                        ->title('password update success')
                        ->success()
                        ->send();
                })

            ,

        ];
    }
}