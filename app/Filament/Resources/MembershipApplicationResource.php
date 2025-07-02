<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipApplicationResource\Pages;
use App\Models\MembershipApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class MembershipApplicationResource extends Resource
{
    protected static ?string $model = MembershipApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $navigationLabel = 'Lidmaatschap Aanvragen';
    
    protected static ?string $modelLabel = 'Lidmaatschap Aanvraag';
    
    protected static ?string $pluralModelLabel = 'Lidmaatschap Aanvragen';

    protected static ?string $navigationGroup = 'Leden Beheer';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('voornaam')
                    ->required()
                    ->maxLength(255),
                TextInput::make('achternaam')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('telefoonnummer')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                DatePicker::make('geboortedatum')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-Y'),
                Select::make('status')
                    ->options([
                        'nieuw' => 'Nieuw',
                        'in_behandeling' => 'In behandeling',
                        'goedgekeurd' => 'Goedgekeurd',
                        'afgekeurd' => 'Afgekeurd',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('voornaam')
                    ->searchable(),
                TextColumn::make('achternaam')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('telefoonnummer')
                    ->searchable(),
                TextColumn::make('geboortedatum')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('leeftijd')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nieuw' => 'gray',
                        'in_behandeling' => 'warning',
                        'goedgekeurd' => 'success',
                        'afgekeurd' => 'danger',
                    }),
                TextColumn::make('aangemeld_op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListMembershipApplications::route('/'),
            'create' => Pages\CreateMembershipApplication::route('/create'),
            'edit' => Pages\EditMembershipApplication::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'nieuw')->count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getWidgets(): array
    {
        return [];
    }
}
