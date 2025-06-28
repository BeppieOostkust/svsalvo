<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadResource\Pages;
use App\Models\Download;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    
    protected static ?string $navigationLabel = 'Downloads';
    
    protected static ?int $navigationSort = 20;
    
    protected static ?string $modelLabel = 'download';
    
    protected static ?string $pluralModelLabel = 'downloads';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Download Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Categorie')
                            ->options([
                                'reglementen' => 'Reglementen',
                                'formulieren' => 'Formulieren',
                                'resultaten' => 'Resultaten',
                                'documenten' => 'Documenten',
                                'fotos' => "Foto's",
                            ])
                            ->required()
                            ->default('documenten'),
                        Forms\Components\Select::make('uploaded_by')
                            ->label('Uploader')
                            ->relationship('uploader', 'name')
                            ->required()
                            ->default(auth()->id()),
                    ])
                    ->columns(2),
                    
                Section::make('Beschrijving & Bestand')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Beschrijving')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Beschrijving van het bestand en wat het bevat'),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Bestand')
                            ->required()
                            ->maxSize(2048) // 2MB
                            ->helperText('Max 2MB - PDF, Word, afbeeldingen, tekstbestanden')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('file_name')
                            ->label('Bestandsnaam')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),
                        Forms\Components\TextInput::make('file_type')
                            ->label('Bestandstype')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),
                        Forms\Components\TextInput::make('file_size')
                            ->label('Bestandsgrootte (bytes)')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),
                    ]),
                    
                Section::make('Toegang & Metadata')
                    ->schema([
                        Forms\Components\Select::make('access_level')
                            ->label('Toegangsniveau')
                            ->options([
                                'public' => 'Openbaar - Iedereen kan downloaden',
                                'members' => 'Alleen leden - Login vereist',
                                'roles' => 'Specifieke rollen - Selecteer hieronder',
                            ])
                            ->default('public')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-update the boolean fields based on selection
                                $set('is_public', $state === 'public');
                                $set('requires_login', $state !== 'public');
                                
                                // Clear roles if not role-based access
                                if ($state !== 'roles') {
                                    $set('allowed_roles', []);
                                }
                            }),
                        Forms\Components\Select::make('allowed_roles')
                            ->label('Toegestane rollen')
                            ->multiple()
                            ->options([
                                'member' => 'Lid (alle geverifieerde gebruikers)',
                                'bestuur' => 'Bestuurslid',
                                'trainer' => 'Trainer/Instructeur',
                                'competitor' => 'Wedstrijdschutter',
                                'youth' => 'Jeugdlid',
                                'admin' => 'Administrator',
                            ])
                            ->placeholder('Selecteer welke rollen toegang hebben...')
                            ->helperText('Als je niets selecteert, hebben alle ingelogde gebruikers toegang. Als je specifieke rollen selecteert, hebben alleen gebruikers met die rollen toegang.')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->hidden(fn (callable $get) => $get('access_level') !== 'roles'),
                        Forms\Components\TextInput::make('is_public')
                            ->hidden()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('requires_login')
                            ->hidden()
                            ->dehydrated(),
                        Forms\Components\Placeholder::make('access_info')
                            ->label('Wie heeft toegang?')
                            ->content(function (callable $get) {
                                $accessLevel = $get('access_level');
                                
                                switch ($accessLevel) {
                                    case 'public':
                                        return 'Iedereen kan dit bestand downloaden';
                                    case 'members':
                                        return 'Alleen ingelogde gebruikers kunnen dit bestand downloaden';
                                    case 'roles':
                                        $roles = $get('allowed_roles') ?? [];
                                        if (empty($roles)) {
                                            return 'Selecteer eerst specifieke rollen';
                                        }
                                        
                                        $roleLabels = [
                                            'member' => 'Leden',
                                            'bestuur' => 'Bestuursleden',
                                            'trainer' => 'Trainers/Instructeurs',
                                            'competitor' => 'Wedstrijdschutters',
                                            'youth' => 'Jeugdleden',
                                            'admin' => 'Administrators',
                                        ];
                                        
                                        $selectedRoles = array_map(fn($role) => $roleLabels[$role] ?? $role, $roles);
                                        return 'Alleen toegankelijk voor: ' . implode(', ', $selectedRoles);
                                    default:
                                        return 'Selecteer een toegangsniveau';
                                }
                            })
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                        Forms\Components\TextInput::make('download_count')
                            ->label('Aantal downloads')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'reglementen' => 'primary',
                        'formulieren' => 'success',
                        'resultaten' => 'info',
                        'documenten' => 'warning',
                        'fotos' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Openbaar')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\IconColumn::make('requires_login')
                    ->label('Login vereist')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('info')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Grootte')
                    ->formatStateUsing(fn (?int $state): string => 
                        $state ? number_format($state / 1024 / 1024, 2) . ' MB' : 'Onbekend'
                    ),
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Toegevoegd')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categorie')
                    ->options([
                        'reglementen' => 'Reglementen',
                        'formulieren' => 'Formulieren',
                        'resultaten' => 'Resultaten',
                        'documenten' => 'Documenten',
                        'fotos' => "Foto's",
                    ]),
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Openbaar')
                    ->placeholder('Alle downloads')
                    ->trueLabel('Alleen openbare downloads')
                    ->falseLabel('Alleen private downloads'),
                Tables\Filters\TernaryFilter::make('requires_login')
                    ->label('Login vereist')
                    ->placeholder('Alle downloads')
                    ->trueLabel('Login vereist')
                    ->falseLabel('Geen login vereist'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Download $record): string => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->label('Bewerken'),
                Tables\Actions\DeleteAction::make()
                    ->label('Verwijderen'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Geselecteerde verwijderen'),
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

    public static function getNavigationGroup(): ?string
    {
        return 'Algemene Beheer';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDownloads::route('/'),
            'create' => Pages\CreateDownload::route('/create'),
            'edit' => Pages\EditDownload::route('/{record}/edit'),
        ];
    }
}
