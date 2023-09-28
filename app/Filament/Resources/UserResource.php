<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Regency;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use App\Models\Province;
use App\Models\Villages;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
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
                    ->afterStateUpdated(

                        function (Set $set) {
                            $set('regency_id', null);
                            $set('district_id', null);
                            $set('village_id', null);
                        }

                    )
                    ->live(),

                Select::make('regency_id')
                    ->options(
                        fn(Get $get): Collection => Regency::query()
                            ->where('province_id', $get('province_id'))
                            ->pluck('name', 'id')
                    )
                    ->afterStateUpdated(
                        function (Set $set) {

                            $set('district_id', null);
                            $set('village_id', null);
                        }
                    )
                    ->live()
                    ->searchable()
                ,

                Select::make('district_id')
                    ->options(fn(Get $get): Collection => District::query()
                        ->where('regency_id', $get('regency_id'))
                        ->pluck('name', 'id'))
                    ->afterStateUpdated(fn(Set $set) => $set('village_id', null))

                    ->searchable()
                    ->live(),

                Select::make('village_id')
                    ->options(fn(Get $get): Collection => Villages::query()
                        ->where('district_id', $get('district_id'))
                        ->pluck('name', 'id'))
                    ->searchable()
                ,

                TextInput::make('baru')
                    ->default(function (Get $get): Collection {
                        $collection = new Collection();

                        // Check for province ID
                        if ($get('province_id')) {

                            // Query regencies by ID
                            $regencies = Regency::where('province_id', $get('province_id'))
                                ->first();

                            // Collect results
                            $collection = collect($regencies);

                        }

                        // Return collection
                        return $collection;
                    }),

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