<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalDocumentResource\Pages;
use App\Models\LegalDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Enums\FontWeight;

class LegalDocumentResource extends Resource
{
    protected static ?string $model = LegalDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Juridische Documenten';
    
    protected static ?string $modelLabel = 'Juridisch Document';
    
    protected static ?string $pluralModelLabel = 'Juridische Documenten';
    
    protected static ?string $navigationGroup = 'Beheer';

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
                Select::make('type')
                    ->label('Type Document')
                    ->options([
                        'privacy_policy' => 'Privacy Policy',
                        'terms_conditions' => 'Algemene Voorwaarden',
                    ])
                    ->required()
                    ->native(false),
                    
                TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('version')
                    ->label('Versie')
                    ->required()
                    ->default('1.0')
                    ->maxLength(20),
                    
                RichEditor::make('content')
                    ->label('Inhoud')
                    ->required()
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('changes_summary')
                    ->label('Samenvatting van Wijzigingen')
                    ->rows(4)
                    ->helperText('Beschrijf kort wat er is veranderd (gebruikt in email notificaties)')
                    ->columnSpanFull(),
                    
                DateTimePicker::make('effective_date')
                    ->label('Ingangsdatum')
                    ->required()
                    ->default(now()),
                    
                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(false)
                    ->helperText('Alleen één document per type kan actief zijn')
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        if ($state) {
                            // Deactivate other documents of the same type
                            LegalDocument::where('type', $get('type'))
                                ->where('is_active', true)
                                ->update(['is_active' => false]);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'privacy_policy' => 'info',
                        'terms_conditions' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'privacy_policy' => 'Privacy Policy',
                        'terms_conditions' => 'Algemene Voorwaarden',
                        default => $state,
                    }),
                    
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->weight(FontWeight::Medium),
                    
                TextColumn::make('version')
                    ->label('Versie'),
                    
                BooleanColumn::make('is_active')
                    ->label('Actief')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                    
                TextColumn::make('effective_date')
                    ->label('Ingangsdatum')
                    ->dateTime('d-m-Y H:i'),
                    
                TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'privacy_policy' => 'Privacy Policy',
                        'terms_conditions' => 'Algemene Voorwaarden',
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
            'index' => Pages\ListLegalDocuments::route('/'),
            'create' => Pages\CreateLegalDocument::route('/create'),
            'edit' => Pages\EditLegalDocument::route('/{record}/edit'),
        ];
    }
}
