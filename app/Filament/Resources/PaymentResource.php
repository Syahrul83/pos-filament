<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\PaymentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentResource\RelationManagers;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Payment Date')
                    ->date('d/m/Y H')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->color('success')
                    ->url(fn(Payment $record) => ProductResource::getUrl('edit', ['record' => $record->product])),
                TextColumn::make('user.name')->label('User Name')
                    ->color('warning')
                    ->url(fn(Payment $record) => UserResource::getUrl('edit', ['record' => $record->user])),
                TextColumn::make('user.email')->label('User Email'),
                TextColumn::make('voucher.code')->label('Voucher Code'),
                TextColumn::make('subtotal')->money('idr'),
                TextColumn::make('taxes')->money('idr'),
                TextColumn::make('total')->money('idr')->summarize(Sum::make()->money('idr')),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['created_from'],
                            fn($query) =>
                            $query->whereDate('created_at', '>=', $data['created_from'])
                        )
                            ->when(
                                $data['created_until'],
                                fn($query) =>
                                $query->whereDate('created_at', '<=', $data['created_until'])
                            )

                        ;
                    })
                ,
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            // 'create' => Pages\CreatePayment::route('/create'),
            // 'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}