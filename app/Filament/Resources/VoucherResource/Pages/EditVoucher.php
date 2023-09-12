<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\VoucherResource;

class EditVoucher extends EditRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeFill()
    {
        if ($this->record->payments()->exists()) {
            return [
                Notification::make()
                    ->title('Voucer sudah di gunakan')
                    ->danger()
                    ->send(),
                $this->redirect($this->getResource()::getUrl('index')),

            ];
        }

    }
}