<?php

namespace App\Filament\Resources\MatchesResource\Pages;

use App\Filament\Resources\MatchesResource;
use App\Models\User;
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
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('linker_kaart_7')
                                        ->label('Aantal schoten in 7 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('linker_kaart_8')
                                        ->label('Aantal schoten in 8 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('linker_kaart_9')
                                        ->label('Aantal schoten in 9 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('linker_kaart_10')
                                        ->label('Aantal schoten in 10 links')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                ])
                                ->columns(5),
                            
                            Section::make('Rechter Kaart Scores')
                                ->schema([
                                    TextInput::make('rechter_kaart_6')
                                        ->label('Aantal schoten in 6 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('rechter_kaart_7')
                                        ->label('Aantal schoten in 7 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('rechter_kaart_8')
                                        ->label('Aantal schoten in 8 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('rechter_kaart_9')
                                        ->label('Aantal schoten in 9 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('rechter_kaart_10')
                                        ->label('Aantal schoten in 10 rechts')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                ])
                                ->columns(5),
                            
                            Section::make('Penalties')
                                ->schema([
                                    TextInput::make('aantal_schoten_buiten_tijd')
                                        ->label('Schoten buiten de tijd')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                    
                                    TextInput::make('afwaarderingen')
                                        ->label('Afwaarderingen')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $this->updateTotalPoints($get, $set)),
                                ])
                                ->columns(2),
                            
                            TextInput::make('totale_punten')
                                ->label('Totale Punten')
                                ->numeric()
                                ->disabled()
                                ->default(0),
                        ])
                        ->columns(1)
                        ->itemLabel(fn (array $state): ?string => 
                            User::find($state['gebruiker_id'] ?? null)?->name ?? 'Nieuwe Speler'
                        )
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
        // Calculate points from score fields by multiplying count with score value
        $total = 
            (int)($get('linker_kaart_6') ?? 0) * 6 +
            (int)($get('linker_kaart_7') ?? 0) * 7 +
            (int)($get('linker_kaart_8') ?? 0) * 8 +
            (int)($get('linker_kaart_9') ?? 0) * 9 +
            (int)($get('linker_kaart_10') ?? 0) * 10 +
            (int)($get('rechter_kaart_6') ?? 0) * 6 +
            (int)($get('rechter_kaart_7') ?? 0) * 7 +
            (int)($get('rechter_kaart_8') ?? 0) * 8 +
            (int)($get('rechter_kaart_9') ?? 0) * 9 +
            (int)($get('rechter_kaart_10') ?? 0) * 10 -
            ((int)($get('aantal_schoten_buiten_tijd') ?? 0) * 2) -
            (int)($get('afwaarderingen') ?? 0);
            
        $set('totale_punten', $total);
    }
}