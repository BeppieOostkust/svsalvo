<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource\RelationManagers;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationGroup = 'Email Beheer';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naam')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (unieke identifier)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Bijv: new-user-temp-password, new-match, etc.')
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
                            ])
                            ->default('system')
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actief')
                            ->required()
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Email Inhoud')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Onderwerp')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Gebruik {{variabele}} voor dynamische waarden')
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('html_content')
                            ->label('HTML Inhoud')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'heading',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'undo',
                                'redo',
                            ])
                            ->helperText('Gebruik {{variabele}} voor dynamische waarden')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('text_content')
                            ->label('Platte tekst versie (optioneel)')
                            ->rows(8)
                            ->helperText('Automatisch gegenereerd als leeg gelaten')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Beschikbare Variabelen')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Beschrijving')
                            ->rows(3)
                            ->helperText('Beschrijf waar deze template voor gebruikt wordt')
                            ->columnSpanFull(),
                        
                        Forms\Components\TagsInput::make('available_variables')
                            ->label('Beschikbare Variabelen')
                            ->helperText('Voeg beschikbare variabelen toe (zonder {{ }}). Bijv: name, email, temporary_password')
                            ->placeholder('Voeg variabele toe...')
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
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
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
                Tables\Columns\TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actief')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('emailLogs_count')
                    ->counts('emailLogs')
                    ->label('Verzonden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
