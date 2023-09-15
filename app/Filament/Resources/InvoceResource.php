<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Invoice as Invoce;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\InvoceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoceResource\RelationManagers;

class InvoceResource extends Resource
{
    protected static ?string $model = Invoce::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('invoice_number')
                                    ->required()
                                    ->default('ABC-' . random_int(1000, 9999)),
                                DatePicker::make('invoice_date')
                                    ->default(now()->format('d-m-Y'))
                                    ->required(),

                            ])->columns([
                                    'sm' => 2
                                ]),
                        Card::make()
                            ->schema([
                                Placeholder::make('Product'),
                                Repeater::make('invoiceItems')
                                    ->relationship()
                                    ->defaultItems(1)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::query()->pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {

                                                $product = Product::find($state);
                                                if ($product) {
                                                    # code...
                                                    $set('price', number_format($product->price / 100, 2));
                                                    $set('product_price', $product->price);

                                                }

                                            })


                                            ->columnSpan([
                                                'md' => 5,
                                            ]),

                                        TextInput::make('product_amount')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->columnSpan([
                                                'md' => 2,
                                            ])

                                        ,
                                        Hidden::make('product_price'),
                                        TextInput::make('price')
                                            ->disabled()
                                            //dehydated false agar tidak tersave otomatis
                                            ->dehydrated(false)
                                            ->numeric()
                                            ->columnSpan([
                                                'md' => 3,
                                            ]),


                                    ])

                            ])


                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_date')->date()->sortable(),
                TextColumn::make('invoice_number')->sortable(),
                TextColumn::make('user.name')->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListInvoces::route('/'),
            'create' => Pages\CreateInvoce::route('/create'),
            'edit' => Pages\EditInvoce::route('/{record}/edit'),
        ];
    }
}