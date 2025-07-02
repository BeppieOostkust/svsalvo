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

class EditMatches extends EditRecord
{
    protected static string $resource = MatchesResource::class;

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
            Actions\DeleteAction::make(),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Initialize audio player
        $this->dispatch('init-audio-player');
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
                ->schema([
                    Repeater::make('gebruikersScores')
                        ->relationship('gebruikersScores')                
                        ->schema([
                            Select::make('kaliber')
                            ->options([
                                'kkp' => 'KKP (Klein Kaliber Pistool)',
                                'gkp' => 'GKP (Groot Kaliber Pistool)',
                            ])
                            ->required(),
                            Select::make('gebruiker_id')
                                ->label('Speler')
                                ->options(options: User::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            
                            Section::make('Linker Kaart Scores')
                                ->schema([
                                    TextInput::make('linker_kaart_6')
                                        ->label('Aantal schoten in 6 links')
                                        ->numeric()
                                        ->default(0)
                                        ->required()
                                        ->validationAttribute('Aantal schoten in 6 links'),
                                    
                                    TextInput::make('linker_kaart_7')
                                        ->label('Aantal schoten in 7 links')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_8')
                                        ->label('Aantal schoten in 8 links')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_9')
                                        ->label('Aantal schoten in 9 links')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_10')
                                        ->label('Aantal schoten in 10 links')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                ])
                                ->columns(5)
                                ->extraAttributes(function (callable $get) {
                                    $leftCard6 = (int)$get('linker_kaart_6');
                                    $leftCard7 = (int)$get('linker_kaart_7');
                                    $leftCard8 = (int)$get('linker_kaart_8');
                                    $leftCard9 = (int)$get('linker_kaart_9');
                                    $leftCard10 = (int)$get('linker_kaart_10');
                                    
                                    $leftTotal = $leftCard6 + $leftCard7 + $leftCard8 + $leftCard9 + $leftCard10;
                                    
                                    if ($leftTotal > 12) {
                                        return [
                                            'class' => 'border-danger-600 bg-danger-50',
                                            'data-tooltip-content' => 'Telfout! Tel opnieuw! Meer dan 12 schoten op linker kaart.',
                                            'data-tooltip-placement' => 'top',
                                        ];
                                    }
                                    
                                    return [];
                                }),
                            
                            Section::make('Rechter Kaart Scores')
                                ->schema([
                                    TextInput::make('rechter_kaart_6')
                                        ->label('Aantal schoten in 6 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_7')
                                        ->label('Aantal schoten in 7 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_8')
                                        ->label('Aantal schoten in 8 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_9')
                                        ->label('Aantal schoten in 9 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_10')
                                        ->label('Aantal schoten in 10 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                ])
                                ->columns(5)
                                ->extraAttributes(function (callable $get) {
                                    $rightCard6 = (int)$get('rechter_kaart_6');
                                    $rightCard7 = (int)$get('rechter_kaart_7');
                                    $rightCard8 = (int)$get('rechter_kaart_8');
                                    $rightCard9 = (int)$get('rechter_kaart_9');
                                    $rightCard10 = (int)$get('rechter_kaart_10');
                                    
                                    $rightTotal = $rightCard6 + $rightCard7 + $rightCard8 + $rightCard9 + $rightCard10;
                                    
                                    if ($rightTotal > 12) {
                                        return [
                                            'class' => 'border-danger-600 bg-danger-50',
                                            'data-tooltip-content' => 'Telfout! Tel opnieuw! Meer dan 12 schoten op rechter kaart.',
                                            'data-tooltip-placement' => 'top',
                                        ];
                                    }
                                    
                                    return [];
                                }),
                            
                            Section::make('Penalties')
                                ->schema([
                                    TextInput::make('aantal_schoten_buiten_tijd')
                                        ->label('Schoten buiten de tijd')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    
                                    TextInput::make('afwaarderingen')
                                        ->label('Afwaarderingen')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                ])
                                ->columns(2),
                            
                            TextInput::make('totale_punten')
                                ->label('Totale Punten')
                                ->numeric()
                                ->disabled()
                                ->default(0)
                                ->extraAttributes(function (callable $get) {
                                    $totalPoints = (int)$get('totale_punten');
                                    $leftCard6 = (int)$get('linker_kaart_6');
                                    $leftCard7 = (int)$get('linker_kaart_7');
                                    $leftCard8 = (int)$get('linker_kaart_8');
                                    $leftCard9 = (int)$get('linker_kaart_9');
                                    $leftCard10 = (int)$get('linker_kaart_10');
                                    $rightCard6 = (int)$get('rechter_kaart_6');
                                    $rightCard7 = (int)$get('rechter_kaart_7');
                                    $rightCard8 = (int)$get('rechter_kaart_8');
                                    $rightCard9 = (int)$get('rechter_kaart_9');
                                    $rightCard10 = (int)$get('rechter_kaart_10');
                                    
                                    $leftTotal = $leftCard6 + $leftCard7 + $leftCard8 + $leftCard9 + $leftCard10;
                                    $rightTotal = $rightCard6 + $rightCard7 + $rightCard8 + $rightCard9 + $rightCard10;
                                    
                                    $hasWarning = $totalPoints > 240 || $leftTotal > 12 || $rightTotal > 12;
                                    
                                    if ($hasWarning) {
                                        return [
                                            'class' => 'text-danger-600 font-bold',
                                            'data-tooltip-content' => 'Telfout! Tel opnieuw!',
                                            'data-tooltip-placement' => 'top',
                                            'style' => 'position: relative;'
                                        ];
                                    }
                                    
                                    return [];
                                })
                                ->suffixIcon(function (callable $get) {
                                    $totalPoints = (int)$get('totale_punten');
                                    $leftCard6 = (int)$get('linker_kaart_6');
                                    $leftCard7 = (int)$get('linker_kaart_7');
                                    $leftCard8 = (int)$get('linker_kaart_8');
                                    $leftCard9 = (int)$get('linker_kaart_9');
                                    $leftCard10 = (int)$get('linker_kaart_10');
                                    $rightCard6 = (int)$get('rechter_kaart_6');
                                    $rightCard7 = (int)$get('rechter_kaart_7');
                                    $rightCard8 = (int)$get('rechter_kaart_8');
                                    $rightCard9 = (int)$get('rechter_kaart_9');
                                    $rightCard10 = (int)$get('rechter_kaart_10');
                                    
                                    $leftTotal = $leftCard6 + $leftCard7 + $leftCard8 + $leftCard9 + $leftCard10;
                                    $rightTotal = $rightCard6 + $rightCard7 + $rightCard8 + $rightCard9 + $rightCard10;
                                    
                                    if ($totalPoints > 240 || $leftTotal > 12 || $rightTotal > 12) {
                                        return 'heroicon-o-exclamation-circle';
                                    }
                                    
                                    return null;
                                }),
                        ])
                        ->columns(1)
                        ->itemLabel(function (array $state): ?string {
                            $name = User::find($state['gebruiker_id'] ?? null)?->name ?? 'Nieuwe Speler';
                            $kaliber = $state['kaliber'] ?? '';
                            $points = $state['totale_punten'] ?? '';
                            
                            $kaliberDisplay = '';
                            if ($kaliber === 'gkp') {
                                $kaliberDisplay = 'GKP';
                            } elseif ($kaliber === 'kkp') {
                                $kaliberDisplay = 'KKP';
                            }
                            
                            if ($points !== '') {
                                return "$name - $kaliberDisplay - Totaal: $points punten";
                            }
                            
                            return "$name" . ($kaliberDisplay ? " - $kaliberDisplay" : "");
                        })
                        ->addActionLabel('Speler toevoegen')
                        ->deleteAction(
                            fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation()
                        )
                        ->collapsible()
                        ->defaultItems(0),
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
                    $warningMessages[] = "{$playerName}: Totaal punten hoger dan 240!";
                }
                
                // Check for more than 12 shots on one side
                $leftTotal = $this->countTotalShots([
                    $scoreData['linker_kaart_6'] ?? 0,
                    $scoreData['linker_kaart_7'] ?? 0,
                    $scoreData['linker_kaart_8'] ?? 0,
                    $scoreData['linker_kaart_9'] ?? 0,
                    $scoreData['linker_kaart_10'] ?? 0
                ]);
                
                $rightTotal = $this->countTotalShots([
                    $scoreData['rechter_kaart_6'] ?? 0,
                    $scoreData['rechter_kaart_7'] ?? 0,
                    $scoreData['rechter_kaart_8'] ?? 0,
                    $scoreData['rechter_kaart_9'] ?? 0,
                    $scoreData['rechter_kaart_10'] ?? 0
                ]);
                
                if ($leftTotal > 12 || $rightTotal > 12) {
                    $hasWarning = true;
                    $playerName = User::find($scoreData['gebruiker_id'] ?? null)?->name ?? 'Onbekende speler';
                    $warningMessages[] = "{$playerName}: Meer dan 12 schoten op één kaart!";
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
        
        // Calculate the total points
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
}