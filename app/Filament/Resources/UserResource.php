<?php

namespace App\Filament\Resources;

use App\Models\District;
use App\Models\Villages;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Regency;
use Filament\Forms\Get;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Collection;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';


    // recode title artibute
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Setting';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->required()->email(),
                Select::make('province_id')
                    ->options(Province::query()->pluck('name', 'id'))
                    ->searchable()
                    ->live(),

                Select::make('regency_id')
                    ->options(fn(Get $get): Collection => Regency::query()
                        ->where('province_id', $get('province_id'))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->live(),

                Select::make('district_id')
                    ->options(fn(Get $get): Collection => District::query()
                        ->where('regency_id', $get('regency_id'))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->live(),

                Select::make('village_id')
                    ->options(fn(Get $get): Collection => Villages::query()
                        ->where('district_id', $get('district_id'))
                        ->pluck('name', 'id'))
                    ->searchable()
                ,

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('name')->sortable()->label('Name'),
                TextColumn::make('email')->sortable()->label('Email'),
                TextColumn::make('created_at')
                    ->date('d/m/Y H')
                    ->sortable(),

            ])->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->form([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        // ...
                    ]),
                Tables\Actions\EditAction::make(),

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

                    ])->action(function (User $record, array $data) {
                        $record->update([
                            'password' => bcrypt($data['new_password'])
                        ]);
                        Notification::make()
                            ->title('password update success')
                            ->success()
                            ->send();
                    })

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


    //  filament global search arttribute
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email'
        ];
    }




}