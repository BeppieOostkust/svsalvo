<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RuleResource\Pages;
use App\Filament\Resources\RuleResource\RelationManagers;
use App\Models\Rule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Regels';

    protected static ?string $navigationGroup = 'Content Beheer';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('content')
                    ->label('Inhoud')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('category')
                    ->label('Categorie')
                    ->maxLength(255)
                    ->placeholder('Bijv: Algemeen, Veiligheid, Gedrag'),
                
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
                    ->options(fn () => Rule::distinct('category')->pluck('category', 'category')->toArray()),
                
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
            'index' => Pages\ListRules::route('/'),
            'create' => Pages\CreateRule::route('/create'),
            'edit' => Pages\EditRule::route('/{record}/edit'),
        ];
    }
}
