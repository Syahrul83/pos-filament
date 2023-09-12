<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // redirect url filament to index
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    // function mutate data befor cerate
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['price'] = $data['price'] * 100;
    //     return $data;
    // }
}