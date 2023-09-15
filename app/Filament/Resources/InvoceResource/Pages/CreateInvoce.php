<?php

namespace App\Filament\Resources\InvoceResource\Pages;

use App\Filament\Resources\InvoceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoce extends CreateRecord
{
    protected static string $resource = InvoceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;

    }
}