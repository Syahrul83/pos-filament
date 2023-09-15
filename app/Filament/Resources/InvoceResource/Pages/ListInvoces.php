<?php

namespace App\Filament\Resources\InvoceResource\Pages;

use App\Filament\Resources\InvoceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoces extends ListRecords
{
    protected static string $resource = InvoceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
