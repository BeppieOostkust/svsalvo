<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $modelLabel = 'Instelling';

    protected static ?string $pluralModelLabel = 'Instellingen';

    protected static ?string $navigationGroup = 'Beheer';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->canAccessAll() || 
               auth()->user()?->hasRole('Webmaster') || 
               auth()->user()?->hasRole('Secretaris');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccessAll() || 
               auth()->user()?->hasRole('Webmaster') || 
               auth()->user()?->hasRole('Secretaris');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Sleutel')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Beschrijving')
                    ->maxLength(500),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'string' => 'Tekst',
                        'boolean' => 'Waar/Onwaar',
                        'integer' => 'Getal',
                        'json' => 'JSON',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('value')
                    ->label('Waarde')
                    ->required(),
                Forms\Components\TextInput::make('group')
                    ->label('Groep')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_public')
                    ->label('Publiek zichtbaar'),
                Forms\Components\Toggle::make('is_editable')
                    ->label('Bewerkbaar'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Groep')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Sleutel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Waarde')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->value;
                    }),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publiek')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_editable')
                    ->label('Bewerkbaar')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Groep')
                    ->options(function () {
                        return Setting::distinct()->pluck('group', 'group')->toArray();
                    }),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'string' => 'Tekst',
                        'boolean' => 'Waar/Onwaar',
                        'integer' => 'Getal',
                        'json' => 'JSON',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->is_editable && (
                        auth()->user()?->canAccessAll() || 
                        auth()->user()?->hasRole('Webmaster') || 
                        auth()->user()?->hasRole('Secretaris')
                    )),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->canAccessAll()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
