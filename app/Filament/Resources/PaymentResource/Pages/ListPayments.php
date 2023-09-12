<?php

namespace App\Filament\Resources\PaymentResource\Pages;


use Filament\Actions;
use Illuminate\Contracts\View\View;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PaymentResource;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getTableContentFooter(): View
    {
        return view('filament/payments/footer');
    }
}