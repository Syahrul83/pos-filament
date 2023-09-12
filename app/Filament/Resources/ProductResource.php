<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Illuminate\Support\Str;
use Forms\Components\Upload;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // make filament input text name, slug
                TextInput::make('name')
                    ->required()

                    ->unique()
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->live(debounce: 1000),

                // make automatic slug form above textInput name
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),

                // make filament image upload
                FileUpload::make('image'),

                Forms\Components\TextInput::make('price')
                    ->rules('numeric')
                    ->required(),

                // Select::make('category')
                //     ->options([
                //         'web' => 'Web development',
                //         'mobile' => 'Mobile development',
                //         'design' => 'Design',
                //     ])
                //     ->searchable()
                //     ->live(),

                // Select::make('sub_category')
                //     ->options(fn(Get $get): array => match ($get('category')) {
                //         'web' => [
                //             'frontend_web' => 'Frontend development',
                //             'backend_web' => 'Backend development',
                //         ],
                //         'mobile' => [
                //             'ios_mobile' => 'iOS development',
                //             'android_mobile' => 'Android development',
                //         ],
                //         'design' => [
                //             'app_design' => 'Panel design',
                //             'marketing_website_design' => 'Marketing website design',
                //         ],
                //         default => [],
                //     })->searchable(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // filametn text column number order
                TextColumn::make('No')->rowIndex(),
                ImageColumn::make('image')->width(50),

                // text column name
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                // text column price
                TextColumn::make('price')
                    ->prefix('Rp')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                    ->sortable(),

            ])->defaultSort('id', 'desc')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}