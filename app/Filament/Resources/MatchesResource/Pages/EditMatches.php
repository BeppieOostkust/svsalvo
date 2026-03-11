<?php

namespace App\Filament\Resources\MatchesResource\Pages;

use App\Filament\Resources\MatchesResource;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Log;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Colors\Color;

class EditMatches extends EditRecord
{
    protected static string $resource = MatchesResource::class;

    // Enable polling to check for updates every 5 seconds
    protected static ?string $pollingInterval = '5s';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('saveOnly')
                ->label('Alleen opslaan')
                ->color('success')
                ->icon('heroicon-o-check')
                ->action(function () {
                    $this->save();
                    
                    // Don't redirect, just notify
                    $warningMessages = session('score_warnings', []);
                    
                    if (!empty($warningMessages)) {
                        Notification::make()
                            ->title('Let op! Mogelijke telfouten gedetecteerd')
                            ->warning()
                            ->body(implode("\n", $warningMessages))
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Scores opgeslagen')
                            ->success()
                            ->send();
                    }
                }),
            Actions\Action::make('eindewedstrijd')
                ->label('EINDE WEDSTRIJD')
                ->color('warning')
                ->icon('heroicon-o-trophy')
                ->modalHeading('🏆 Wedstrijd Uitslagen')
                ->modalDescription('Overzicht van alle winnaars per kaliber, gegroepeerd per serie')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Sluiten')
                ->modalContent(function () {
                    return view('filament.pages.match-results', [
                        'results' => $this->getMatchResults()
                    ]);
                })
                ->modalWidth('7xl'),
            Actions\DeleteAction::make(),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Initialize audio player
        $this->dispatch('init-audio-player');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Sort gebruikersScores by round_number, then kaliber (KKP first), then totale_punten descending
        if (isset($data['gebruikersScores']) && is_array($data['gebruikersScores'])) {
            usort($data['gebruikersScores'], function ($a, $b) {
                // First sort by round_number (ascending)
                $roundCompare = ($a['round_number'] ?? 1) <=> ($b['round_number'] ?? 1);
                if ($roundCompare !== 0) {
                    return $roundCompare;
                }
                
                // Then sort by kaliber (KKP before GKP)
                $kaliberA = strtolower($a['kaliber'] ?? '');
                $kaliberB = strtolower($b['kaliber'] ?? '');
                $kaliberCompare = 0;
                if ($kaliberA === 'kkp' && $kaliberB === 'gkp') {
                    $kaliberCompare = -1;
                } elseif ($kaliberA === 'gkp' && $kaliberB === 'kkp') {
                    $kaliberCompare = 1;
                }
                if ($kaliberCompare !== 0) {
                    return $kaliberCompare;
                }
                
                // Finally sort by totale_punten (descending)
                return ($b['totale_punten'] ?? 0) <=> ($a['totale_punten'] ?? 0);
            });
        }
        
        return $data;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            // Match details section
            Section::make('Wedstrijd Details')
                ->schema([
                    TextInput::make('naam')
                        ->label('Naam')
                        ->required(),
                    Textarea::make('beschrijving')
                        ->label('Beschrijving')
                        ->rows(3),
                    Select::make('status')
                        ->options([
                            'binnenkort' => 'Binnenkort',
                            'bezig' => 'Bezig',
                            'afgelopen' => 'Afgelopen',
                            'geannuleerd' => 'Geannuleerd',
                        ])
                        ->required(),
                    DateTimePicker::make('start_datum')
                        ->label('Startdatum')
                        ->required(),
                ])
                ->columns(2),

            // Player scores section
            Section::make('Speler Scores')
                ->description('Beheer de scores van alle spelers voor deze wedstrijd.')
                ->schema([
                    // Search bar for players
                    TextInput::make('player_search')
                        ->label('🔍 Zoek Speler')
                        ->placeholder('Zoek op naam, email of kaliber... (bijv. "Jan", "kkp", "gkp")')
                        ->live(debounce: 500)
                        ->helperText('Typ om te zoeken door alle spelergegevens en kalibers')
                        ->extraAttributes([
                            'style' => 'margin-bottom: 1.5rem; font-size: 1.1em;',
                            'class' => 'search-input-enhanced'
                        ])
                        ->suffixIcon('heroicon-o-magnifying-glass')
                        ->dehydrated(false) // This prevents the field from being included in the form data
                        ->default('') // Set a proper default value
                        ->afterStateUpdated(function ($state, $set) {
                            // This ensures the search field works properly without affecting the model
                            // The filtering logic can be implemented here if needed
                        }),
                        
                    Repeater::make('gebruikersScores')
                        ->relationship('gebruikersScores')
                        ->schema([
                            Grid::make(4)
                                ->schema([
                                    Select::make('gebruiker_id')
                                        ->label('Speler')
                                        ->options(function () {
                                            return User::all()->mapWithKeys(function ($user) {
                                                $displayName = $user->first_name && $user->last_name 
                                                    ? $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'
                                                    : $user->name . ' (' . $user->email . ')';
                                                return [$user->id => $displayName];
                                            });
                                        })
                                        ->searchable()
                                        ->required()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return User::where('name', 'like', "%{$search}%")
                                                ->orWhere('first_name', 'like', "%{$search}%")
                                                ->orWhere('last_name', 'like', "%{$search}%")
                                                ->orWhere('email', 'like', "%{$search}%")
                                                ->limit(50)
                                                ->get()
                                                ->mapWithKeys(function ($user) {
                                                    $displayName = $user->first_name && $user->last_name 
                                                        ? $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'
                                                        : $user->name . ' (' . $user->email . ')';
                                                    return [$user->id => $displayName];
                                                });
                                        })
                                        ->placeholder('Zoek een speler...')
                                        ->columnSpan(1),
                                    Select::make('kaliber')
                                        ->options([
                                            'kkp' => 'KKP (Klein Kaliber Pistool)',
                                            'gkp' => 'GKP (Groot Kaliber Pistool)',
                                        ])
                                        ->required()
                                        ->columnSpan(1),
                                    Select::make('round_number')
                                        ->label('Serie')
                                        ->options([
                                            1 => '1e Serie',
                                            2 => '2e Serie',
                                            3 => '3e Serie',
                                            4 => '4e Serie',
                                            5 => '5e Serie',
                                            6 => '6e Serie',
                                            7 => '7e Serie',
                                            8 => '8e Serie',
                                            9 => '9e Serie',
                                            10 => '10e Serie',
                                        ])
                                        ->default(1)
                                        ->required()
                                        ->columnSpan(1),
                                    TextInput::make('baan_nummer')
                                        ->label('🎯 Baan')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(20)
                                        ->placeholder('1-20')
                                        ->helperText('Baan nummer (1-20)')
                                        ->columnSpan(1),
                                ]),
                            
                            Grid::make(2)
                                ->schema([
                                    Toggle::make('is_official')
                                        ->label('Officiële Score')
                                        ->helperText('Vink aan als dit de officiële score is die telt voor de ranglijst')
                                        ->default(true)
                                        ->columnSpan(1),
                                    Placeholder::make('score_info')
                                        ->label('')
                                        ->content(function (callable $get) {
                                            if ($get('is_official')) {
                                                return '✅ Deze score telt mee voor de ranglijst';
                                            }
                                            return '🎯 Deze score is voor de fun/oefening';
                                        })
                                        ->columnSpan(1),
                                ]),
                            
                            Section::make('Linker Kaart Scores')
                                ->schema([
                                    Grid::make(6)
                                        ->schema([
                                            TextInput::make('linker_kaart_5')
                                                ->label('5-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->helperText('0 punten')
                                            
                                                ->columnSpan(1),
                                            TextInput::make('linker_kaart_6')
                                                ->label('6-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('linker_kaart_7')
                                                ->label('7-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('linker_kaart_8')
                                                ->label('8-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('linker_kaart_9')
                                                ->label('9-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('linker_kaart_10')
                                                ->label('10-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                        ])
                                ])
                                ->collapsible()
                                ->collapsed(true) // Altijd ingeklapt
                                ->extraAttributes(function (callable $get) {
                                    $leftTotal = $this->countTotalShots([
                                        $get('linker_kaart_5') ?? 0,
                                        $get('linker_kaart_6') ?? 0,
                                        $get('linker_kaart_7') ?? 0,
                                        $get('linker_kaart_8') ?? 0,
                                        $get('linker_kaart_9') ?? 0,
                                        $get('linker_kaart_10') ?? 0
                                    ]);
                                    
                                    if ($leftTotal > 12) {
                                        return [
                                            'class' => 'border-danger-600 bg-danger-50',
                                            'data-tooltip' => 'Telfout! Meer dan 12 schoten op linker kaart.',
                                        ];
                                    }
                                    
                                    return [];
                                }),
                            
                            Section::make('Rechter Kaart Scores')
                                ->schema([
                                    Grid::make(6)
                                        ->schema([
                                            TextInput::make('rechter_kaart_5')
                                                ->label('5-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->helperText('0 punten')
                                                ->columnSpan(1),
                                            TextInput::make('rechter_kaart_6')
                                                ->label('6-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('rechter_kaart_7')
                                                ->label('7-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('rechter_kaart_8')
                                                ->label('8-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('rechter_kaart_9')
                                                ->label('9-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('rechter_kaart_10')
                                                ->label('10-ring')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                        ])
                                ])
                                ->collapsible()
                                ->collapsed(true) // Altijd ingeklapt
                                ->extraAttributes(function (callable $get) {
                                    $rightTotal = $this->countTotalShots([
                                        $get('rechter_kaart_5') ?? 0,
                                        $get('rechter_kaart_6') ?? 0,
                                        $get('rechter_kaart_7') ?? 0,
                                        $get('rechter_kaart_8') ?? 0,
                                        $get('rechter_kaart_9') ?? 0,
                                        $get('rechter_kaart_10') ?? 0
                                    ]);
                                    
                                    if ($rightTotal > 12) {
                                        return [
                                            'class' => 'border-danger-600 bg-danger-50',
                                            'data-tooltip' => 'Telfout! Meer dan 12 schoten op rechter kaart.',
                                        ];
                                    }
                                    
                                    return [];
                                }),
                            
                            Section::make('Penalties & Totaal')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('aantal_schoten_buiten_tijd')
                                                ->label('Schoten buiten tijd')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('afwaarderingen')
                                                ->label('Afwaarderingen')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->columnSpan(1),
                                            TextInput::make('totale_punten')
                                                ->label('Totale Punten')
                                                ->numeric()
                                                ->disabled()
                                                ->default(0)
                                                ->columnSpan(1)
                                                ->extraAttributes(function (callable $get) {
                                                    $totalPoints = (int)$get('totale_punten');
                                                    $leftTotal = $this->countTotalShots([
                                                        $get('linker_kaart_5') ?? 0,
                                                        $get('linker_kaart_6') ?? 0,
                                                        $get('linker_kaart_7') ?? 0,
                                                        $get('linker_kaart_8') ?? 0,
                                                        $get('linker_kaart_9') ?? 0,
                                                        $get('linker_kaart_10') ?? 0
                                                    ]);
                                                    $rightTotal = $this->countTotalShots([
                                                        $get('rechter_kaart_5') ?? 0,
                                                        $get('rechter_kaart_6') ?? 0,
                                                        $get('rechter_kaart_7') ?? 0,
                                                        $get('rechter_kaart_8') ?? 0,
                                                        $get('rechter_kaart_9') ?? 0,
                                                        $get('rechter_kaart_10') ?? 0
                                                    ]);
                                                    
                                                    if ($totalPoints > 240 || $leftTotal > 12 || $rightTotal > 12) {
                                                        return [
                                                            'class' => 'text-danger-600 font-bold',
                                                            'data-tooltip' => 'Telfout! Controleer de scores!',
                                                        ];
                                                    }
                                                    
                                                    return [];
                                                }),
                                        ])
                                ])
                                ->collapsible()
                                ->collapsed(true), // Altijd ingeklapt
                            
                            Textarea::make('notes')
                                ->label('Notities')
                                ->placeholder('Optionele notities over deze score...')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->itemLabel(function (array $state): ?string {
                            $user = User::find($state['gebruiker_id'] ?? null);
                            $name = $user?->name ?? 'Nieuwe Speler';
                            $kaliber = $state['kaliber'] ?? '';
                            $round = $state['round_number'] ?? 1;
                            $points = $state['totale_punten'] ?? '';
                            $isOfficial = $state['is_official'] ?? true;
                            $baan = $state['baan_nummer'] ?? '';
                            
                            $kaliberDisplay = $kaliber === 'gkp' ? 'GKP' : ($kaliber === 'kkp' ? 'KKP' : '');
                            
                            $officialBadge = $isOfficial ? '✅' : '🎯';
                            $pointsDisplay = $points !== '' ? " ({$points}pt)" : '';
                            $baanDisplay = $baan !== '' ? " [Baan {$baan}]" : '';
                            
                            // Format: "📊 SERIE 1 | ✅ John Doe - GKP [Baan 5] (224pt)"
                            return "📊 SERIE {$round} | {$officialBadge} {$name} - {$kaliberDisplay}{$baanDisplay}{$pointsDisplay}";
                        })
                        ->addActionLabel('Speler toevoegen')
                        ->deleteAction(
                            fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation()
                        )
                        ->collapsible()
                        ->collapsed(true)
                        ->cloneable()
                        ->reorderableWithButtons()
                        ->defaultItems(0)
                        ->addActionLabel('➕ Nieuwe Score Toevoegen')
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            // Set default values when creating new score
                            $data['round_number'] = $data['round_number'] ?? 1;
                            $data['is_official'] = $data['is_official'] ?? true;
                            return $data;
                        })

                ]),
        ]);
    }
    
    // Calculate points for all players when saving
    protected function beforeSave(): void
    {
        $data = $this->data;
        $hasWarning = false;
        $warningMessages = [];
        
        if (isset($data['gebruikersScores']) && is_array($data['gebruikersScores'])) {
            foreach ($data['gebruikersScores'] as $index => $scoreData) {
                $total = $this->calculateTotalPoints($scoreData);
                $this->data['gebruikersScores'][$index]['totale_punten'] = $total;
                
                // Check for high scores
                if ($total > 240) {
                    $hasWarning = true;
                    $playerName = User::find($scoreData['gebruiker_id'] ?? null)?->name ?? 'Onbekende speler';
                    $roundName = match($scoreData['round_number'] ?? 1) {
                        1 => '1e Serie',
                        2 => '2e Serie',
                        3 => '3e Serie',
                        4 => '4e Serie',
                        5 => '5e Serie',
                        6 => '6e Serie',
                        7 => '7e Serie',
                        8 => '8e Serie',
                        9 => '9e Serie',
                        10 => '10e Serie',
                        default => "Serie {$scoreData['round_number']}"
                    };
                    $warningMessages[] = "{$playerName} ({$roundName}): Totaal punten hoger dan 240!";
                }
                
                // Check for more than 12 shots on one side (inclusief 5-ring)
                $leftTotal = $this->countTotalShots([
                    $scoreData['linker_kaart_5'] ?? 0,
                    $scoreData['linker_kaart_6'] ?? 0,
                    $scoreData['linker_kaart_7'] ?? 0,
                    $scoreData['linker_kaart_8'] ?? 0,
                    $scoreData['linker_kaart_9'] ?? 0,
                    $scoreData['linker_kaart_10'] ?? 0
                ]);
                
                $rightTotal = $this->countTotalShots([
                    $scoreData['rechter_kaart_5'] ?? 0,
                    $scoreData['rechter_kaart_6'] ?? 0,
                    $scoreData['rechter_kaart_7'] ?? 0,
                    $scoreData['rechter_kaart_8'] ?? 0,
                    $scoreData['rechter_kaart_9'] ?? 0,
                    $scoreData['rechter_kaart_10'] ?? 0
                ]);                if ($leftTotal > 12 || $rightTotal > 12) {
                    $hasWarning = true;
                    $playerName = User::find($scoreData['gebruiker_id'] ?? null)?->name ?? 'Onbekende speler';
                    $roundName = match($scoreData['round_number'] ?? 1) {
                        1 => '1e Serie',
                        2 => '2e Serie',
                        3 => '3e Serie',
                        4 => '4e Serie',
                        5 => '5e Serie',
                        6 => '6e Serie',
                        7 => '7e Serie',
                        8 => '8e Serie',
                        9 => '9e Serie',
                        10 => '10e Serie',
                        default => "Serie {$scoreData['round_number']}"
                    };
                    $warningMessages[] = "{$playerName} ({$roundName}): Meer dan 12 schoten op één kaart!";
                }
            }
        }
        
        // Store warnings in session for use after save
        session()->flash('score_warnings', $warningMessages);
    }
    
    // Helper method to count total shots on one side
    private function countTotalShots(array $values): int
    {
        $total = 0;
        foreach ($values as $value) {
            $total += (int)$value;
        }
        return $total;
    }
    
    // Notification after successful save
    protected function afterSave(): void
    {
        // Broadcast real-time update (only if Reverb is running)
        try {
            broadcast(new \App\Events\MatchUpdated('updated', $this->record->id));
        } catch (\Exception $e) {
            // Silently fail if broadcasting is not available
        }
        
        $warningMessages = session('score_warnings', []);
        
        if (!empty($warningMessages)) {
            // Play warning sound when errors are detected
            $this->dispatch('play-sound', soundPath: asset('sounds/notification.mp3'));
            
            Notification::make()
                ->title('Let op! Mogelijke telfouten gedetecteerd')
                ->warning()
                ->body(implode("\n", $warningMessages))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('check')
                        ->label('Controleer scores')
                        ->url($this->getResource()::getUrl('edit', ['record' => $this->record])),
                ])
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title('Scores opgeslagen')
                ->success()
                ->send();
        }
    }
    
    // Calculate total points for a single player
    private function calculateTotalPoints(array $data): int
    {
        // Safe conversion to integers with fallback to 0 if empty
        // 5-ring telt NIET mee voor punten (0 punten per schot)
        $leftCard6 = !empty($data['linker_kaart_6']) ? (int)$data['linker_kaart_6'] : 0;
        $leftCard7 = !empty($data['linker_kaart_7']) ? (int)$data['linker_kaart_7'] : 0;
        $leftCard8 = !empty($data['linker_kaart_8']) ? (int)$data['linker_kaart_8'] : 0;
        $leftCard9 = !empty($data['linker_kaart_9']) ? (int)$data['linker_kaart_9'] : 0;
        $leftCard10 = !empty($data['linker_kaart_10']) ? (int)$data['linker_kaart_10'] : 0;
        
        $rightCard6 = !empty($data['rechter_kaart_6']) ? (int)$data['rechter_kaart_6'] : 0;
        $rightCard7 = !empty($data['rechter_kaart_7']) ? (int)$data['rechter_kaart_7'] : 0;
        $rightCard8 = !empty($data['rechter_kaart_8']) ? (int)$data['rechter_kaart_8'] : 0;
        $rightCard9 = !empty($data['rechter_kaart_9']) ? (int)$data['rechter_kaart_9'] : 0;
        $rightCard10 = !empty($data['rechter_kaart_10']) ? (int)$data['rechter_kaart_10'] : 0;
        
        $outOfTime = !empty($data['aantal_schoten_buiten_tijd']) ? (int)$data['aantal_schoten_buiten_tijd'] : 0;
        $penalties = !empty($data['afwaarderingen']) ? (int)$data['afwaarderingen'] : 0;
        
        // Calculate the total points (5-ring = 0 punten, maar telt wel als schot)
        return ($leftCard6 * 6) +
               ($leftCard7 * 7) +
               ($leftCard8 * 8) +
               ($leftCard9 * 9) +
               ($leftCard10 * 10) +
               ($rightCard6 * 6) +
               ($rightCard7 * 7) +
               ($rightCard8 * 8) +
               ($rightCard9 * 9) +
               ($rightCard10 * 10) -
               ($outOfTime * 2) -
               $penalties;
    }
    
    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }

    // For refreshing list after save
    protected function getRedirectUrl(): ?string
    {
        // Check if this was a standard form submit
        // If it's from the "Alleen opslaan" button, we'll handle redirects in the action
        if (request()->has('_index-redirect')) {
            return $this->getResource()::getUrl('index');
        }
        
        // Stay on the same page by default
        return null;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getRelationManagers(): array
    {
        return $this->getResource()::getRelations();
    }

    protected function getMatchResults(): array
    {
        $scores = $this->record->gebruikersScores()
            ->where('is_official', true)
            ->with('gebruiker')
            ->get();

        $results = [];

        // Group by kaliber, then combine all series per player into one ranking
        $groupedByKaliber = $scores->groupBy(function ($score) {
            return strtoupper($score->kaliber);
        });

        foreach ($groupedByKaliber as $kaliber => $kaliberScores) {
            // Combine all series: sum points per player
            $playerTotals = [];

            foreach ($kaliberScores as $score) {
                $userId = $score->gebruiker_id;

                if (!isset($playerTotals[$userId])) {
                    $playerTotals[$userId] = [
                        'name' => $score->gebruiker->name ?? 'Onbekend',
                        'points' => 0,
                        'series_count' => 0,
                        'baan' => $score->baan_nummer,
                    ];
                }

                $playerTotals[$userId]['points'] += $score->totale_punten;
                $playerTotals[$userId]['series_count']++;
            }

            // Sort by total points descending
            usort($playerTotals, fn ($a, $b) => $b['points'] <=> $a['points']);

            $ranked = array_values($playerTotals);
            foreach ($ranked as $i => &$entry) {
                $entry['position'] = $i + 1;
            }
            unset($entry);

            $results[] = [
                'kaliber' => $kaliber,
                'scores' => $ranked,
            ];
        }

        usort($results, function ($a, $b) {
            if ($a['kaliber'] === 'KKP' && $b['kaliber'] === 'GKP') {
                return -1;
            }

            if ($a['kaliber'] === 'GKP' && $b['kaliber'] === 'KKP') {
                return 1;
            }

            return strcmp($a['kaliber'], $b['kaliber']);
        });

        return $results;
    }
}