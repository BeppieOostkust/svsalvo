<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityResource\Pages;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BooleanColumn;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Faciliteiten';
    
    protected static ?string $modelLabel = 'Faciliteit';
    
    protected static ?string $pluralModelLabel = 'Faciliteiten';
    
    protected static ?string $navigationGroup = 'Organisatie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full'),
                    
                Textarea::make('description')
                    ->label('Beschrijving')
                    ->required()
                    ->rows(3)
                    ->columnSpan('full'),
                    
                Select::make('icon_type')
                    ->label('Icoon Type')
                    ->required()
                    ->options([
                        'building' => 'Gebouw',
                        'book' => 'Boek/Bibliotheek',
                        'users' => 'Gebruikers/Groep',
                        'target' => 'Doel/Target',
                        'shield' => 'Schild/Beveiliging',
                    ])
                    ->default('building'),
                    
                Select::make('icon_color')
                    ->label('Icoon Kleur')
                    ->required()
                    ->options([
                        'blue' => 'Blauw',
                        'green' => 'Groen',
                        'red' => 'Rood',
                        'yellow' => 'Geel',
                        'purple' => 'Paars',
                        'pink' => 'Roze',
                        'indigo' => 'Indigo',
                        'gray' => 'Grijs',
                    ])
                    ->default('blue'),
                    
                FileUpload::make('image')
                    ->label('Afbeelding')
                    ->image()
                    ->directory('facilities')
                    ->visibility('public')
                    ->columnSpan('full'),
                    
                TextInput::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->numeric()
                    ->default(0)
                    ->required(),
                    
                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Afbeelding')
                    ->square()
                    ->size(60),
                    
                TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('Beschrijving')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                    
                TextColumn::make('icon_type')
                    ->label('Icoon')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'building' => 'primary',
                        'book' => 'success',
                        'users' => 'warning',
                        'target' => 'danger',
                        'shield' => 'info',
                        default => 'gray',
                    }),
                    
                TextColumn::make('icon_color')
                    ->label('Kleur')
                    ->badge()
                    ->color(fn (string $state): string => $state),
                    
                TextColumn::make('sort_order')
                    ->label('Volgorde')
                    ->sortable(),
                    
                BooleanColumn::make('is_active')
                    ->label('Actief'),
                    
                TextColumn::make('updated_at')
                    ->label('Laatst Bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Alleen actieve faciliteiten')
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
            'index' => Pages\ListFacilities::route('/'),
            'create' => Pages\CreateFacility::route('/create'),
            'edit' => Pages\EditFacility::route('/{record}/edit'),
        ];
    }
}
