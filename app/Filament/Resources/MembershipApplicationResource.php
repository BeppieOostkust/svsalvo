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

class MembershipApplicationResource extends Resource
{
    protected static ?string $model = MembershipApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $navigationLabel = 'Lidmaatschap Aanvragen';
    
    protected static ?string $modelLabel = 'Lidmaatschap Aanvraag';
    
    protected static ?string $pluralModelLabel = 'Lidmaatschap Aanvragen';

    protected static ?string $navigationGroup = 'Leden Beheer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Persoonlijke Gegevens')
                    ->schema([
                        Forms\Components\TextInput::make('voornaam')
                            ->label('Voornaam')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('achternaam')
                            ->label('Achternaam')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mailadres')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telefoonnummer')
                            ->label('Telefoonnummer')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('geboortedatum')
                            ->label('Geboortedatum')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                        Forms\Components\TextInput::make('leeftijd')
                            ->label('Leeftijd')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
                
                Section::make('Status & Opmerkingen')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'nieuw' => 'Nieuw',
                                'in_behandeling' => 'In behandeling',
                                'goedgekeurd' => 'Goedgekeurd',
                                'afgewezen' => 'Afgewezen',
                            ])
                            ->default('nieuw')
                            ->required(),
                        Forms\Components\Textarea::make('opmerkingen')
                            ->label('Opmerkingen')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('aangemeld_op')
                            ->label('Aangemeld op')
                            ->default(now())
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voornaam')
                    ->label('Voornaam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('achternaam')
                    ->label('Achternaam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('telefoonnummer')
                    ->label('Telefoon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('leeftijd')
                    ->label('Leeftijd')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nieuw' => 'warning',
                        'in_behandeling' => 'primary',
                        'goedgekeurd' => 'success',
                        'afgewezen' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nieuw' => 'Nieuw',
                        'in_behandeling' => 'In behandeling',
                        'goedgekeurd' => 'Goedgekeurd',
                        'afgewezen' => 'Afgewezen',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('aangemeld_op')
                    ->label('Aangemeld op')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'nieuw' => 'Nieuw',
                        'in_behandeling' => 'In behandeling',
                        'goedgekeurd' => 'Goedgekeurd',
                        'afgewezen' => 'Afgewezen',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Bekijken'),
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
            ->defaultSort('created_at', 'desc');
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
}
