<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Filament\Resources\EmailLogResource\RelationManagers;
use App\Models\EmailLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Email Beheer';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Email Informatie')
                    ->schema([
                        Forms\Components\Select::make('email_template_id')
                            ->label('Template')
                            ->relationship('emailTemplate', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->label('Gebruiker')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('to_email')
                            ->label('Naar email')
                            ->email()
                            ->disabled(),
                        Forms\Components\TextInput::make('to_name')
                            ->label('Naar naam')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Verzonden op')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Inhoud')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Onderwerp')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('html_content')
                            ->label('HTML Inhoud')
                            ->rows(10)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('text_content')
                            ->label('Tekst Inhoud')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Foutmelding')
                    ->schema([
                        Forms\Components\Textarea::make('error_message')
                            ->label('Foutmelding')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record && $record->status === 'failed'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('emailTemplate.name')
                    ->label('Template')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_email')
                    ->label('Naar')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'sent',
                        'danger' => 'failed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Verzonden')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->placeholder('Niet verzonden'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Gebruiker')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'In afwachting',
                        'sent' => 'Verzonden',
                        'failed' => 'Mislukt',
                    ]),
                Tables\Filters\SelectFilter::make('email_template_id')
                    ->label('Template')
                    ->relationship('emailTemplate', 'name'),
                Tables\Filters\Filter::make('sent_at')
                    ->label('Alleen verzonden')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('sent_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function canCreate(): bool
    {
        return false; // Logs are created automatically
    }
    
    public static function canEdit($record): bool
    {
        return false; // Logs are read-only
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
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }
}
