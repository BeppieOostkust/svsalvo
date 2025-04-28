<?php

namespace App\Filament\Resources\MatchesResource\Pages;

use App\Filament\Resources\MatchesResource;
use App\Models\User;
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

class EditMatches extends EditRecord
{
    protected static string $resource = MatchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
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
                            'upcoming' => 'Binnenkort',
                            'in_progress' => 'Bezig',
                            'completed' => 'Klaar',
                            'cancelled' => 'Geannuleerd',
                        ])
                        ->default('upcoming')
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
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->reactive(),
                            
                            Section::make('Linker Kaart Scores')
                                ->schema([
                                    TextInput::make('linker_kaart_6')
                                        ->label('Aantal schoten in 6 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_7')
                                        ->label('Aantal schoten in 7 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_8')
                                        ->label('Aantal schoten in 8 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_9')
                                        ->label('Aantal schoten in 9 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('linker_kaart_10')
                                        ->label('Aantal schoten in 10 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                ])
                                ->columns(5),
                            
                            Section::make('Rechter Kaart Scores')
                                ->schema([
                                    TextInput::make('rechter_kaart_6')
                                        ->label('Aantal schoten in 6 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_7')
                                        ->label('Aantal schoten in 7 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_8')
                                        ->label('Aantal schoten in 8 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_9')
                                        ->label('Aantal schoten in 9 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('rechter_kaart_10')
                                        ->label('Aantal schoten in 10 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                ])
                                ->columns(5),
                            
                            Section::make('Penalties')
                                ->schema([
                                    TextInput::make('aantal_schoten_buiten_tijd')
                                        ->label('Schoten buiten de tijd')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                    
                                    TextInput::make('afwaarderingen')
                                        ->label('Afwaarderingen')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set))
                                        ->required(),
                                ])
                                ->columns(2),
                            
                            TextInput::make('totale_punten')
                                ->label('Totale Punten')
                                ->numeric()
                                ->disabled()
                                ->default(0),
                        ])
                        ->columns(1)
                        ->itemLabel(function (array $state): ?string {
                            $name = User::find($state['gebruiker_id'] ?? null)?->name ?? 'Nieuwe Speler';
                            $kaliber = $state['kaliber'] ?? ''; // Changed from soort_pistool to kaliber
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
    
    private function updateTotalPoints($get, $set): void
    {

        Log::info('Score values:', [
            'L6' => $get('linker_kaart_6'),
            'L7' => $get('linker_kaart_7'),
            'L8' => $get('linker_kaart_8'),
            'L9' => $get('linker_kaart_9'),
            'L10' => $get('linker_kaart_10'),
            'R6' => $get('rechter_kaart_6'),
            'R7' => $get('rechter_kaart_7'),
            'R8' => $get('rechter_kaart_8'),
            'R9' => $get('rechter_kaart_9'),
            'R10' => $get('rechter_kaart_10'),
            'buiten_tijd' => $get('aantal_schoten_buiten_tijd'),
            'afwaarderingen' => $get('afwaarderingen'),
        ]);

        
        // Safe conversion to integers with fallback to 0 if empty
        $leftCard6 = !empty($get('linker_kaart_6')) ? (int)$get('linker_kaart_6') : 0;
        $leftCard7 = !empty($get('linker_kaart_7')) ? (int)$get('linker_kaart_7') : 0;
        $leftCard8 = !empty($get('linker_kaart_8')) ? (int)$get('linker_kaart_8') : 0;
        $leftCard9 = !empty($get('linker_kaart_9')) ? (int)$get('linker_kaart_9') : 0;
        $leftCard10 = !empty($get('linker_kaart_10')) ? (int)$get('linker_kaart_10') : 0;
        
        $rightCard6 = !empty($get('rechter_kaart_6')) ? (int)$get('rechter_kaart_6') : 0;
        $rightCard7 = !empty($get('rechter_kaart_7')) ? (int)$get('rechter_kaart_7') : 0;
        $rightCard8 = !empty($get('rechter_kaart_8')) ? (int)$get('rechter_kaart_8') : 0;
        $rightCard9 = !empty($get('rechter_kaart_9')) ? (int)$get('rechter_kaart_9') : 0;
        $rightCard10 = !empty($get('rechter_kaart_10')) ? (int)$get('rechter_kaart_10') : 0;
        
        $outOfTime = !empty($get('aantal_schoten_buiten_tijd')) ? (int)$get('aantal_schoten_buiten_tijd') : 0;
        $penalties = !empty($get('afwaarderingen')) ? (int)$get('afwaarderingen') : 0;
        
        // Calculate the total points
        $total = 
            ($leftCard6 * 6) +
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
            
        // Log the calculated total (optional)
        // \Log::info('Calculated total points: ' . $total);
        
        $set('totale_punten', $total);
    }
}