<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Activiteiten';
    
    protected static ?string $navigationGroup = 'Activiteiten Beheer';
    
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessActivities() || $user->is_admin);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessActivities() || $user->is_admin);
    }
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $modelLabel = 'activiteit';
    
    protected static ?string $pluralModelLabel = 'activiteiten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Activiteit Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                            )
                            ->helperText('De titel van de activiteit die zichtbaar zal zijn voor deelnemers'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'training' => 'Training',
                                'wedstrijd' => 'Wedstrijd',
                                'evenement' => 'Evenement',
                                'vergadering' => 'Vergadering',
                                'cursus' => 'Cursus',
                                'toernooi' => 'Toernooi',
                                'competitie' => 'Competitie',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'gepland' => 'Gepland',
                                'bevestigd' => 'Bevestigd',
                                'geannuleerd' => 'Geannuleerd',
                                'uitgesteld' => 'Uitgesteld',
                                'afgelopen' => 'Afgelopen',
                            ])
                            ->required()
                            ->default('gepland'),
                        Forms\Components\Select::make('organizer_id')
                            ->label('Organisator')
                            ->relationship('organizer', 'name')
                            ->required()
                            ->default(auth()->id()),
                    ])
                    ->columns(2),
                    
                Section::make('Beschrijving & Locatie')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Beschrijving')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                            ]),
                        Forms\Components\TextInput::make('location')
                            ->label('Locatie')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('contact_info')
                            ->label('Contactinformatie')
                            ->rows(2),
                    ]),
                    
                Section::make('Datum & Tijd')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Startdatum')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Einddatum')
                            ->after('start_date'),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Starttijd'),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Eindtijd')
                            ->after('start_time'),
                    ])
                    ->columns(2),
                    
                Section::make('Deelname & Inschrijving')
                    ->schema([
                        Forms\Components\Toggle::make('requires_registration')
                            ->label('Inschrijving vereist')
                            ->default(false)
                            ->live(),
                        Forms\Components\DateTimePicker::make('registration_deadline')
                            ->label('Inschrijfdeadline')
                            ->visible(fn (callable $get) => $get('requires_registration')),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Maximum deelnemers')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (callable $get) => $get('requires_registration')),
                        Forms\Components\TextInput::make('entry_fee')
                            ->label('Inschrijfgeld (€)')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->visible(fn (callable $get) => $get('requires_registration')),
                        Forms\Components\Textarea::make('requirements')
                            ->label('Vereisten')
                            ->helperText('Bijv. minimumleeftijd, licentie, etc.')
                            ->rows(2),
                    ])
                    ->columns(2),
                    
                Section::make('Media & Extra Informatie')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Uitgelichte afbeelding')
                            ->image()
                            ->directory('activities')
                            ->imageEditor(),
                        Forms\Components\KeyValue::make('additional_info')
                            ->label('Extra informatie')
                            ->keyLabel('Onderwerp')
                            ->valueLabel('Informatie'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Geen activiteiten gevonden')
            ->emptyStateDescription('Er zijn nog geen activiteiten aangemaakt. Maak je eerste activiteit aan om te beginnen.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Eerste activiteit aanmaken')
                    ->icon('heroicon-o-plus')
                    ->button(),
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Afbeelding')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/placeholder.png'),
                Tables\Columns\TextColumn::make('title')
                    ->label('🔥 TITEL (TEST)')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->limit(50)
                    ->tooltip(function ($record): ?string {
                        return strlen($record->title) > 50 ? $record->title : null;
                    }),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->copyable()
                    ->copyMessage('URL gekopieerd!')
                    ->copyMessageDuration(1500)
                    ->fontFamily('mono')
                    ->color('gray')
                    ->size('sm')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Startdatum')
                    ->date('d-m-Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Einddatum')
                    ->date('d-m-Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Starttijd')
                    ->time('H:i')
                    ->icon('heroicon-o-clock')
                    ->color('secondary'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Eindtijd')
                    ->time('H:i')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->icon(function (string $state): string {
                        return match ($state) {
                            'training' => 'heroicon-o-academic-cap',
                            'wedstrijd' => 'heroicon-o-trophy',
                            'evenement' => 'heroicon-o-star',
                            'vergadering' => 'heroicon-o-users',
                            'cursus' => 'heroicon-o-book-open',
                            'toernooi' => 'heroicon-o-fire',
                            'competitie' => 'heroicon-o-flag',
                            default => 'heroicon-o-calendar',
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'training' => 'primary',
                        'wedstrijd' => 'success',
                        'evenement' => 'warning',
                        'vergadering' => 'info',
                        'cursus' => 'secondary',
                        'toernooi' => 'danger',
                        'competitie' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(function (string $state): string {
                        return match ($state) {
                            'gepland' => 'heroicon-o-clock',
                            'bevestigd' => 'heroicon-o-check-circle',
                            'geannuleerd' => 'heroicon-o-x-circle',
                            'uitgesteld' => 'heroicon-o-pause-circle',
                            'afgelopen' => 'heroicon-o-check-badge',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'gepland' => 'warning',
                        'bevestigd' => 'success',
                        'geannuleerd' => 'danger',
                        'uitgesteld' => 'secondary',
                        'afgelopen' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'gepland' => 'Gepland',
                        'bevestigd' => 'Bevestigd',
                        'geannuleerd' => 'Geannuleerd',
                        'uitgesteld' => 'Uitgesteld',
                        'afgelopen' => 'Afgelopen',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('location')
                    ->label('Locatie')
                    ->limit(30)
                    ->searchable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray')
                    ->tooltip(function ($record): ?string {
                        return strlen($record->location ?? '') > 30 ? $record->location : null;
                    }),
                Tables\Columns\IconColumn::make('requires_registration')
                    ->label('Inschrijving vereist')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('current_participants')
                    ->label('Deelnemers')
                    ->formatStateUsing(function ($record): string {
                        $current = $record->current_participants ?? 0;
                        $max = $record->max_participants;
                        
                        if ($max) {
                            $percentage = ($current / $max) * 100;
                            return "{$current}/{$max}";
                        }
                        
                        return (string) $current;
                    })
                    ->color(function ($record): string {
                        if (!$record->max_participants) return 'gray';
                        
                        $percentage = ($record->current_participants / $record->max_participants) * 100;
                        
                        return match (true) {
                            $percentage >= 90 => 'danger',
                            $percentage >= 70 => 'warning', 
                            $percentage >= 50 => 'success',
                            default => 'primary'
                        };
                    })
                    ->icon('heroicon-o-users')
                    ->alignCenter()
                    ->sortable('current_participants'),
                Tables\Columns\TextColumn::make('organizer.name')
                    ->label('Organisator')
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'training' => 'Training',
                        'wedstrijd' => 'Wedstrijd',
                        'evenement' => 'Evenement',
                        'vergadering' => 'Vergadering',
                        'cursus' => 'Cursus',
                        'toernooi' => 'Toernooi',
                        'competitie' => 'Competitie',
                    ])
                    ->placeholder('Alle types'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'gepland' => 'Gepland',
                        'bevestigd' => 'Bevestigd',
                        'geannuleerd' => 'Geannuleerd',
                        'uitgesteld' => 'Uitgesteld',
                        'afgelopen' => 'Afgelopen',
                    ])
                    ->placeholder('Alle statussen'),
                SelectFilter::make('organizer_id')
                    ->label('Organisator')
                    ->relationship('organizer', 'name')
                    ->placeholder('Alle organisatoren'),
                Tables\Filters\TernaryFilter::make('requires_registration')
                    ->label('Inschrijving vereist')
                    ->placeholder('Alle activiteiten')
                    ->trueLabel('Alleen met inschrijving')
                    ->falseLabel('Alleen zonder inschrijving'),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Komende activiteiten')
                    ->query(fn ($query) => $query->where('start_date', '>=', now()))
                    ->toggle()
                    ->default(),
                Tables\Filters\Filter::make('this_month')
                    ->label('Deze maand')
                    ->query(fn ($query) => $query->whereBetween('start_date', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]))
                    ->toggle(),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Bekijken'),
                Tables\Actions\EditAction::make()
                    ->label('Bewerken'),
                Tables\Actions\DeleteAction::make()
                    ->label('Verwijderen')
                    ->modalHeading('Activiteit verwijderen')
                    ->modalDescription('Weet je zeker dat je deze activiteit wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')
                    ->modalSubmitActionLabel('Verwijderen')
                    ->modalCancelActionLabel('Annuleren'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Geselecteerde verwijderen')
                        ->modalHeading('Geselecteerde activiteiten verwijderen')
                        ->modalDescription('Weet je zeker dat je de geselecteerde activiteiten wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')
                        ->modalSubmitActionLabel('Verwijderen')
                        ->modalCancelActionLabel('Annuleren'),
                ]),
            ])
            ->defaultSort('start_date', 'desc')
            ->searchPlaceholder('Zoek activiteiten...')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
