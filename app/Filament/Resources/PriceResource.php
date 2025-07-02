<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceResource\Pages;
use App\Filament\Resources\PriceResource\RelationManagers;
use App\Models\Price;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceResource extends Resource
{
    protected static ?string $model = Price::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';

    protected static ?string $navigationLabel = 'Prijzen';

    protected static ?string $navigationGroup = 'Financieel Beheer';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessFinancial() || $user->is_admin);
    }

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('description')
                    ->label('Beschrijving')
                    ->rows(3)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('amount')
                    ->label('Bedrag')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                
                Forms\Components\Select::make('currency')
                    ->label('Valuta')
                    ->options([
                        'EUR' => 'Euro (€)',
                        'USD' => 'Dollar ($)',
                    ])
                    ->default('EUR')
                    ->required(),
                
                Forms\Components\TextInput::make('category')
                    ->label('Categorie')
                    ->maxLength(255)
                    ->placeholder('Bijv: Lidmaatschap, Cursus, Materiaal'),
                
                Forms\Components\Select::make('period')
                    ->label('Periode')
                    ->options([
                        'per jaar' => 'Per jaar',
                        'per maand' => 'Per maand',
                        'per kwartaal' => 'Per kwartaal',
                        'eenmalig' => 'Eenmalig',
                        'per keer' => 'Per keer',
                    ])
                    ->placeholder('Selecteer periode'),
                
                Forms\Components\TextInput::make('order')
                    ->label('Volgorde')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lager nummer = eerder in de lijst'),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Bedrag')
                    ->money('EUR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Categorie')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                
                Tables\Columns\TextColumn::make('order')
                    ->label('Volgorde')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actief')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categorie')
                    ->options(fn () => Price::distinct('category')->pluck('category', 'category')->toArray()),
                
                Tables\Filters\SelectFilter::make('period')
                    ->label('Periode')
                    ->options([
                        'per jaar' => 'Per jaar',
                        'per maand' => 'Per maand',
                        'per kwartaal' => 'Per kwartaal',
                        'eenmalig' => 'Eenmalig',
                        'per keer' => 'Per keer',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actief'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
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
            'index' => Pages\ListPrices::route('/'),
            'create' => Pages\CreatePrice::route('/create'),
            'edit' => Pages\EditPrice::route('/{record}/edit'),
        ];
    }
}
