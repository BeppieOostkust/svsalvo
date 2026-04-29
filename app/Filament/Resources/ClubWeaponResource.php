<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubWeaponResource\Pages;
use App\Models\ClubWeapon;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClubWeaponResource extends Resource
{
    protected static ?string $model = ClubWeapon::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Clubwapens';

    protected static ?string $modelLabel = 'Clubwapen';

    protected static ?string $pluralModelLabel = 'Clubwapens';

    protected static ?string $navigationGroup = 'Organisatie';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full'),

                Select::make('weapon_type')
                    ->label('Type wapen')
                    ->required()
                    ->options([
                        'GKG' => 'GKG',
                        'KKG' => 'KKG',
                        'KKP' => 'KKP',
                        'GKP' => 'GKP',
                    ]),

                TextInput::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),

                FileUpload::make('image')
                    ->label('Foto van wapen')
                    ->image()
                    ->directory('club-weapons')
                    ->visibility('public')
                    ->required()
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->square()
                    ->size(60),

                TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('weapon_type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'GKG' => 'primary',
                        'KKG' => 'success',
                        'KKP' => 'warning',
                        'GKP' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('sort_order')
                    ->label('Volgorde')
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Actief'),

                TextColumn::make('updated_at')
                    ->label('Laatst bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Alleen actieve clubwapens')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Bewerken'),
                Tables\Actions\DeleteAction::make()
                    ->label('Verwijderen'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Verwijderen'),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClubWeapons::route('/'),
            'create' => Pages\CreateClubWeapon::route('/create'),
            'edit' => Pages\EditClubWeapon::route('/{record}/edit'),
        ];
    }
}
