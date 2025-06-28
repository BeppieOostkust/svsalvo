<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationInfoResource\Pages;
use App\Models\OrganizationInfo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;

class OrganizationInfoResource extends Resource
{
    protected static ?string $model = OrganizationInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Organisatie Info';
    
    protected static ?string $modelLabel = 'Organisatie Informatie';
    
    protected static ?string $pluralModelLabel = 'Organisatie Informatie';
    
    protected static ?string $navigationGroup = 'Organisatie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('section')
                    ->label('Sectie')
                    ->options([
                        'mission' => 'Missie',
                        'vision' => 'Visie', 
                        'history' => 'Geschiedenis',
                        'about' => 'Over Ons',
                        'values' => 'Waarden',
                    ])
                    ->required()
                    ->columnSpan('full'),
                    
                TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full'),
                    
                Textarea::make('content')
                    ->label('Inhoud')
                    ->required()
                    ->rows(5)
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
                BadgeColumn::make('section')
                    ->label('Sectie')
                    ->colors([
                        'primary' => 'mission',
                        'success' => 'vision',
                        'warning' => 'history',
                        'info' => 'about',
                        'secondary' => 'values',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mission' => 'Missie',
                        'vision' => 'Visie',
                        'history' => 'Geschiedenis',
                        'about' => 'Over Ons',
                        'values' => 'Waarden',
                        default => $state,
                    }),
                    
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('content')
                    ->label('Inhoud')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                    
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
                Tables\Filters\SelectFilter::make('section')
                    ->label('Sectie')
                    ->options([
                        'mission' => 'Missie',
                        'vision' => 'Visie',
                        'history' => 'Geschiedenis',
                        'about' => 'Over Ons',
                        'values' => 'Waarden',
                    ]),
                Tables\Filters\Filter::make('is_active')
                    ->label('Alleen actieve items')
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
            ->defaultSort('section')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizationInfos::route('/'),
            'create' => Pages\CreateOrganizationInfo::route('/create'),
            'edit' => Pages\EditOrganizationInfo::route('/{record}/edit'),
        ];
    }
}
