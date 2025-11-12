<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSettingResource\Pages;
use App\Filament\Resources\EmailSettingResource\RelationManagers;
use App\Models\EmailSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailSettingResource extends Resource
{
    protected static ?string $model = EmailSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationGroup = 'Email Beheer';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Instelling Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naam')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('key')
                            ->label('Sleutel')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Moet overeenkomen met template slug')
                            ->columnSpan(1),
                        Forms\Components\Select::make('category')
                            ->label('Categorie')
                            ->required()
                            ->options([
                                'user' => 'Gebruiker',
                                'match' => 'Wedstrijd',
                                'activity' => 'Activiteit',
                                'feedback' => 'Feedback',
                                'legal' => 'Juridisch',
                                'system' => 'Systeem',
                                'notifications' => 'Notificaties',
                            ])
                            ->default('notifications')
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('enabled')
                            ->label('Ingeschakeld')
                            ->required()
                            ->default(true)
                            ->helperText('Schakel uit om deze email notificatie te deactiveren')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Beschrijving')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Beschrijving')
                            ->rows(3)
                            ->helperText('Beschrijf wanneer deze email wordt verzonden')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Sleutel')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user' => 'success',
                        'match' => 'warning',
                        'activity' => 'info',
                        'feedback' => 'primary',
                        'legal' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('enabled')
                    ->label('Ingeschakeld')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        // Log the change
                        \Illuminate\Support\Facades\Log::info("Email setting '{$record->key}' " . ($state ? 'enabled' : 'disabled'));
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Beschrijving')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categorie')
                    ->options([
                        'user' => 'Gebruiker',
                        'match' => 'Wedstrijd',
                        'activity' => 'Activiteit',
                        'feedback' => 'Feedback',
                        'legal' => 'Juridisch',
                        'system' => 'Systeem',
                        'notifications' => 'Notificaties',
                    ]),
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Ingeschakeld'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->is_admin || $user->hasAnyRole(['secretaris', 'webmaster', 'voorzitter']));
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
            'index' => Pages\ListEmailSettings::route('/'),
            'create' => Pages\CreateEmailSetting::route('/create'),
            'edit' => Pages\EditEmailSetting::route('/{record}/edit'),
        ];
    }
}
