<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ExportScoresAction;
use App\Filament\Resources\MatchesResource\Pages;
use App\Filament\Resources\MatchesResource\RelationManagers;
use App\Models\Matches;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class MatchesResource extends Resource
{
    protected static ?string $model = Matches::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Wedstrijd Beheer';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessMatches() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wedstrijd Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('naam')
                            ->label('Wedstrijd Naam')
                            ->required(),
                        Forms\Components\TextInput::make('beschrijving')
                            ->label('Beschrijving'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'binnenkort' => 'Binnenkort',
                                'bezig' => 'Bezig',
                                'geannuleerd' => 'Geannuleerd',
                                'afgelopen' => 'Afgelopen',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('start_datum')
                            ->label('Start Datum')
                            ->required()
                            ->default(now())
                            ->displayFormat('d-m-Y H:i:s')
                            ->seconds(false)
                            ->timezone('Europe/Amsterdam'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Speler Scores (Georganiseerd per Serie)')
                    ->schema([
                        Forms\Components\Repeater::make('gebruikersScores')
                            ->relationship()
                            ->schema([
                                Forms\Components\Section::make('Speler & Serie Info')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('gebruiker_id')
                                                    ->relationship('user', 'name')
                                                    ->label('Speler')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Select::make('kaliber')
                                                    ->label('Kaliber')
                                                    ->options([
                                                        'gkp' => 'GKP',
                                                        'kkp' => 'KKP',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('round_number')
                                                    ->label('Serie #')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->maxValue(10)
                                                    ->required()
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('baan_nummer')
                                                    ->label('🎯 Baan Nummer')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(20)
                                                    ->placeholder('1-20')
                                                    ->helperText('Wijs een baan toe (1-20)')
                                                    ->required()
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Toggle::make('is_official')
                                                    ->label('Officieel')
                                                    ->default(true)
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Placeholder::make('space')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),
                                    
                                Forms\Components\Grid::make(12)
                                    ->schema([
                                        Forms\Components\TextInput::make('linker_kaart_5')
                                            ->label('L-5')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1)
                                            ->helperText('0 pt'),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_6')
                                            ->label('L-6')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_7')
                                            ->label('L-7')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_8')
                                            ->label('L-8')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_9')
                                            ->label('L-9')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_10')
                                            ->label('L-10')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_5')
                                            ->label('R-5')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1)
                                            ->helperText('0 pt'),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_6')
                                            ->label('R-6')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_7')
                                            ->label('R-7')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_8')
                                            ->label('R-8')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_9')
                                            ->label('R-9')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_10')
                                            ->label('R-10')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                    ]),
                                    
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('aantal_schoten_buiten_tijd')
                                            ->label('Buiten Tijd')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('afwaarderingen')
                                            ->label('Afwaarderingen')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('totale_punten')
                                            ->label('Totaal')
                                            ->numeric()
                                            ->disabled()
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('notes')
                                            ->label('Opmerkingen')
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->itemLabel(function (array $state): ?string {
                                $user = \App\Models\User::find($state['gebruiker_id'] ?? null);
                                $userName = $user ? $user->name : 'Onbekend';
                                $serie = $state['round_number'] ?? '?';
                                $kaliber = strtoupper($state['kaliber'] ?? 'onbekend');
                                return "{$userName} - {$serie}e Serie - {$kaliber}";
                            })
                            ->collapsible()
                            ->cloneable()
                            ->addActionLabel('Score Toevoegen')
                            ->reorderableWithButtons()
                            ->orderColumn('round_number')
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('beschrijving')
                    ->label('Beschrijving')
                    ->limit(50),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'binnenkort' => 'Binnenkort',
                        'bezig' => 'Bezig',
                        'geannuleerd' => 'Geannuleerd',
                        'afgelopen' => 'Afgelopen',
                    ]),
                Tables\Columns\TextColumn::make('start_datum')
                    ->label('Start Datum')
                    ->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('gebruikersScores_count')
                    ->label('Aantal Scores')
                    ->counts('gebruikersScores'),
                Tables\Columns\TextColumn::make('aantal_spelers')
                    ->label('Spelers')
                    ->getStateUsing(function ($record) {
                        return $record->gebruikersScores()
                            ->distinct('gebruiker_id')
                            ->count('gebruiker_id');
                    }),
                Tables\Columns\TextColumn::make('aantal_series')
                    ->label('Series')
                    ->getStateUsing(function ($record) {
                        return $record->gebruikersScores()
                            ->distinct('round_number')
                            ->count('round_number');
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'binnenkort' => 'Binnenkort',
                        'bezig' => 'Bezig',
                        'geannuleerd' => 'Geannuleerd',
                        'afgelopen' => 'Afgelopen',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('exportScores')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Matches $record) {
                        return self::downloadMatchScoresExcel($record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('yearLeaderboard')
                    ->label('Algemeen Leaderboard')
                    ->icon('heroicon-o-trophy')
                    ->color('warning')
                    ->modalHeading('🏆 Algemeen Leaderboard - Jaar ' . date('Y'))
                    ->modalDescription('Ranglijst over alle wedstrijden van dit jaar')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Sluiten')
                    ->modalContent(fn () => view('filament.pages.year-leaderboard', [
                        'leaderboard' => self::getYearLeaderboard()
                    ]))
                    ->modalWidth('7xl'),
                Tables\Actions\Action::make('exportKKP')
                    ->label('Export KKP Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return self::downloadBestOfFiveExcelSingle('KKP');
                    }),
                Tables\Actions\Action::make('exportGKP')
                    ->label('Export GKP Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function () {
                        return self::downloadBestOfFiveExcelSingle('GKP');
                    }),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return 'Wedstrijden'; // Instead of "Matches"
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Wedstrijd Beheer';
    }

    public static function getModelLabel(): string
    {
        return 'Wedstrijd';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Wedstrijden';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['gebruikersScores' => function ($query) {
                $query->orderBy('round_number', 'asc')
                    ->orderByRaw("CASE WHEN kaliber = 'kkp' THEN 1 WHEN kaliber = 'gkp' THEN 2 ELSE 3 END")
                    ->orderByDesc('totale_punten')
                    ->orderByRaw('linker_kaart_6 + linker_kaart_7 + linker_kaart_8 + linker_kaart_9 + linker_kaart_10 DESC');
            }]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatches::route('/'),
            'create' => Pages\CreateMatches::route('/create'),
            'edit' => Pages\EditMatches::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RegistrationsRelationManager::class,
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessMatches() || $user->is_admin);
    }

    protected static function downloadMatchScoresExcel(Matches $match)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Get scores grouped by round and kaliber
        $scores = $match->gebruikersScores()
            ->where('is_official', true)
            ->with('gebruiker')
            ->orderBy('round_number')
            ->orderByRaw("CASE WHEN kaliber = 'kkp' THEN 1 WHEN kaliber = 'gkp' THEN 2 ELSE 3 END")
            ->orderByDesc('totale_punten')
            ->get();

        $grouped = $scores->groupBy(function ($score) {
            return $score->round_number . '_' . strtoupper($score->kaliber);
        });

        $currentRow = 1;
        
        foreach ($grouped as $key => $groupScores) {
            [$roundNumber, $kaliber] = explode('_', $key);
            
            // Title - merge cells for "Wedstrijd X GK/KK"
            $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", "Wedstrijd {$roundNumber} " . ($kaliber === 'GKP' ? 'GK' : 'KK'));
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            
            // Date - merge cells
            $sheet->mergeCells("D{$currentRow}:G{$currentRow}");
            $sheet->setCellValue("D{$currentRow}", $match->start_datum->format('d F Y'));
            $sheet->getStyle("D{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FF0000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);
            
            $currentRow++;
            
            // Headers
            $headers = ['Ranking:', 'Schutter:', 'KNSA nr:', 'Ver:', 'Schoten:', 'Punten totaal:', 'Punten links:', 'Punten rechts:'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $currentRow, $header);
                $col++;
            }
            
            // Style headers - light blue background
            $sheet->getStyle("A{$currentRow}:H{$currentRow}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ADD8E6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
            ]);
            
            $currentRow++;
            
            // Sort scores by points descending
            $sorted = $groupScores->sortByDesc('totale_punten')->values();
            
            // Data rows
            foreach ($sorted as $index => $score) {
                $position = $index + 1;
                $user = $score->gebruiker;
                
                // Calculate totals
                $totalShots = 0;
                $leftPoints = 0;
                $rightPoints = 0;
                
                // Left card points (excluding 5-ring which is 0 points)
                $leftPoints = ($score->linker_kaart_6 * 6) +
                              ($score->linker_kaart_7 * 7) +
                              ($score->linker_kaart_8 * 8) +
                              ($score->linker_kaart_9 * 9) +
                              ($score->linker_kaart_10 * 10);
                
                // Right card points
                $rightPoints = ($score->rechter_kaart_6 * 6) +
                               ($score->rechter_kaart_7 * 7) +
                               ($score->rechter_kaart_8 * 8) +
                               ($score->rechter_kaart_9 * 9) +
                               ($score->rechter_kaart_10 * 10);
                
                // Total shots (including 5-ring)
                $totalShots = ($score->linker_kaart_5 ?? 0) + ($score->linker_kaart_6 ?? 0) + 
                              ($score->linker_kaart_7 ?? 0) + ($score->linker_kaart_8 ?? 0) + 
                              ($score->linker_kaart_9 ?? 0) + ($score->linker_kaart_10 ?? 0) +
                              ($score->rechter_kaart_5 ?? 0) + ($score->rechter_kaart_6 ?? 0) + 
                              ($score->rechter_kaart_7 ?? 0) + ($score->rechter_kaart_8 ?? 0) + 
                              ($score->rechter_kaart_9 ?? 0) + ($score->rechter_kaart_10 ?? 0);
                
                $sheet->setCellValue("A{$currentRow}", $position);
                $sheet->setCellValue("B{$currentRow}", $user->avg_name ?? $user->name ?? 'Onbekend');
                $sheet->setCellValue("C{$currentRow}", $user->license_number ?? '');
                $sheet->setCellValue("D{$currentRow}", 'MO'); // Vereniging - kan je aanpassen
                $sheet->setCellValue("E{$currentRow}", $totalShots);
                $sheet->setCellValue("F{$currentRow}", $score->totale_punten);
                $sheet->setCellValue("G{$currentRow}", $leftPoints);
                $sheet->setCellValue("H{$currentRow}", $rightPoints);
                
                // Row styling
                $rowStyle = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ];
                
                // Highlight top 3
                if ($position === 1) {
                    $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD700']];
                } elseif ($position === 2) {
                    $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0C0C0']];
                } elseif ($position === 3) {
                    $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CD7F32']];
                }
                
                // Special styling for specific cells
                if ($user->license_number && $user->license_number === 'BP') {
                    $sheet->getStyle("D{$currentRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']], // Yellow
                    ]);
                } else {
                    $sheet->getStyle("D{$currentRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '90EE90']], // Light green
                    ]);
                }
                
                $sheet->getStyle("A{$currentRow}:H{$currentRow}")->applyFromArray($rowStyle);
                
                // Red text for ranking column
                $sheet->getStyle("A{$currentRow}")->getFont()->setColor(new Color('FF0000'));
                
                $currentRow++;
            }
            
            // Add empty rows between groups
            $currentRow += 2;
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        
        // Generate filename
        $filename = 'wedstrijd-' . str_replace(' ', '-', $match->naam) . '-' . now()->format('Y-m-d') . '.xlsx';
        
        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected static function generateMatchScoresCSV(Matches $match): string
    {
        // Keep old CSV function for backward compatibility
        $scores = $match->gebruikersScores()
            ->where('is_official', true)
            ->with('gebruiker')
            ->orderBy('round_number')
            ->orderByRaw("CASE WHEN kaliber = 'kkp' THEN 1 WHEN kaliber = 'gkp' THEN 2 ELSE 3 END")
            ->orderByDesc('totale_punten')
            ->get();

        $csv = "Wedstrijd: {$match->naam}\n";
        $csv .= "Datum: " . $match->start_datum->format('d-m-Y H:i') . "\n\n";
        $csv .= "Serie,Kaliber,Positie,Naam,Baan,Punten\n";

        $grouped = $scores->groupBy(function ($score) {
            return $score->round_number . '_' . strtoupper($score->kaliber);
        });

        foreach ($grouped as $key => $groupScores) {
            [$roundNumber, $kaliber] = explode('_', $key);
            $sorted = $groupScores->sortByDesc('totale_punten')->values();
            
            foreach ($sorted as $index => $score) {
                $position = $index + 1;
                $name = $score->gebruiker->name ?? 'Onbekend';
                $baan = $score->baan_nummer ?? '-';
                $points = $score->totale_punten;
                
                $csv .= "{$roundNumber},{$kaliber},{$position},{$name},{$baan},{$points}\n";
            }
        }

        return $csv;
    }

    protected static function getYearLeaderboard(): array
    {
        $year = date('Y');
        
        // Get all official scores from this year
        $scores = \App\Models\MatchGebruikerScore::query()
            ->whereHas('wedstrijd', function ($query) use ($year) {
                $query->whereYear('start_datum', $year);
            })
            ->where('is_official', true)
            ->with(['gebruiker', 'wedstrijd'])
            ->get();

        // Group by user and kaliber
        $leaderboard = [];
        
        foreach ($scores as $score) {
            $userId = $score->gebruiker_id;
            $kaliber = strtoupper($score->kaliber);
            $key = $userId . '_' . $kaliber;
            
            if (!isset($leaderboard[$key])) {
                $leaderboard[$key] = [
                    'user_id' => $userId,
                    'name' => $score->gebruiker->name ?? 'Onbekend',
                    'kaliber' => $kaliber,
                    'total_points' => 0,
                    'matches_count' => 0,
                    'average_points' => 0,
                    'best_score' => 0,
                    'series_count' => 0,
                ];
            }
            
            $leaderboard[$key]['total_points'] += $score->totale_punten;
            $leaderboard[$key]['matches_count']++;
            $leaderboard[$key]['series_count']++;
            
            if ($score->totale_punten > $leaderboard[$key]['best_score']) {
                $leaderboard[$key]['best_score'] = $score->totale_punten;
            }
        }

        // Calculate averages
        foreach ($leaderboard as &$entry) {
            if ($entry['series_count'] > 0) {
                $entry['average_points'] = round($entry['total_points'] / $entry['series_count'], 2);
            }
        }

        // Convert to array and sort by total points
        $leaderboard = array_values($leaderboard);
        
        // Sort: KKP before GKP, then by total points descending
        usort($leaderboard, function ($a, $b) {
            if ($a['kaliber'] !== $b['kaliber']) {
                return $a['kaliber'] === 'KKP' ? -1 : 1;
            }
            return $b['total_points'] <=> $a['total_points'];
        });

        // Add positions per kaliber
        $currentKaliber = null;
        $position = 0;
        foreach ($leaderboard as &$entry) {
            if ($entry['kaliber'] !== $currentKaliber) {
                $currentKaliber = $entry['kaliber'];
                $position = 1;
            } else {
                $position++;
            }
            $entry['position'] = $position;
        }

        return $leaderboard;
    }

    protected static function downloadYearLeaderboardExcel($kaliber = 'KKP')
    {
        $year = date('Y');
        $kaliberLower = strtolower($kaliber);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Get all matches from this year with scores for this kaliber
        $matches = Matches::whereYear('start_datum', $year)
            ->whereHas('gebruikersScores', function ($query) use ($kaliberLower) {
                $query->where('kaliber', $kaliberLower)
                      ->where('is_official', true);
            })
            ->orderBy('start_datum')
            ->get();
        
        if ($matches->isEmpty()) {
            // No data message
            $sheet->setCellValue('A1', 'Geen wedstrijden gevonden voor ' . $kaliber . ' in ' . $year);
            $filename = 'leaderboard-' . $kaliber . '-' . $year . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
        
        $startCol = 1; // Column A
        $colOffset = 0;
        
        // Process each match
        foreach ($matches as $matchIndex => $match) {
            $currentCol = $startCol + $colOffset;
            
            // Get scores for this match and kaliber
            $scores = $match->gebruikersScores()
                ->where('kaliber', $kaliberLower)
                ->where('is_official', true)
                ->with('gebruiker')
                ->orderByDesc('totale_punten')
                ->get();
            
            if ($scores->isEmpty()) {
                continue;
            }
            
            // Group by round_number (series)
            $seriesGroups = $scores->groupBy('round_number');
            
            foreach ($seriesGroups as $roundNumber => $seriesScores) {
                // Title row - "Wedstrijd X GK/KK"
                $titleText = "Wedstrijd {$roundNumber} " . ($kaliber === 'GKP' ? 'GK' : 'KK');
                self::setCellValueByColumnIndex($sheet, $currentCol, 1, $titleText);
                $sheet->mergeCells(self::columnIndexToLetter($currentCol) . '1:' . self::columnIndexToLetter($currentCol + 2) . '1');
                $sheet->getStyle(self::columnIndexToLetter($currentCol) . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Date row - red text, right aligned
                $dateText = $match->start_datum->format('d F Y');
                self::setCellValueByColumnIndex($sheet, $currentCol + 3, 1, $dateText);
                $sheet->mergeCells(self::columnIndexToLetter($currentCol + 3) . '1:' . self::columnIndexToLetter($currentCol + 6) . '1');
                $sheet->getStyle(self::columnIndexToLetter($currentCol + 3) . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FF0000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                
                // Headers row
                $headers = ['Ranking', 'Schutter', 'KNSA nr', 'Ver', 'Schoten', 'totaal', 'links', 'rechts'];
                for ($i = 0; $i < count($headers); $i++) {
                    self::setCellValueByColumnIndex($sheet, $currentCol + $i, 2, $headers[$i]);
                }
                
                // Style headers - light blue
                $headerRange = self::columnIndexToLetter($currentCol) . '2:' . self::columnIndexToLetter($currentCol + 7) . '2';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ADD8E6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // Data rows
                $sorted = $seriesScores->sortByDesc('totale_punten')->values();
                $row = 3;
                
                foreach ($sorted as $index => $score) {
                    $position = $index + 1;
                    $user = $score->gebruiker;
                    
                    // Calculate points
                    $leftPoints = ($score->linker_kaart_6 * 6) + ($score->linker_kaart_7 * 7) +
                                  ($score->linker_kaart_8 * 8) + ($score->linker_kaart_9 * 9) +
                                  ($score->linker_kaart_10 * 10);
                    
                    $rightPoints = ($score->rechter_kaart_6 * 6) + ($score->rechter_kaart_7 * 7) +
                                   ($score->rechter_kaart_8 * 8) + ($score->rechter_kaart_9 * 9) +
                                   ($score->rechter_kaart_10 * 10);
                    
                    $totalShots = ($score->linker_kaart_5 ?? 0) + ($score->linker_kaart_6 ?? 0) + 
                                  ($score->linker_kaart_7 ?? 0) + ($score->linker_kaart_8 ?? 0) + 
                                  ($score->linker_kaart_9 ?? 0) + ($score->linker_kaart_10 ?? 0) +
                                  ($score->rechter_kaart_5 ?? 0) + ($score->rechter_kaart_6 ?? 0) + 
                                  ($score->rechter_kaart_7 ?? 0) + ($score->rechter_kaart_8 ?? 0) + 
                                  ($score->rechter_kaart_9 ?? 0) + ($score->rechter_kaart_10 ?? 0);
                    
                    // Fill data
                    self::setCellValueByColumnIndex($sheet, $currentCol, $row, $position);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 1, $row, $user->avg_name ?? $user->name ?? '');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 2, $row, $user->license_number ?? '');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 3, $row, 'MO');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 4, $row, $totalShots);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 5, $row, $score->totale_punten);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 6, $row, $leftPoints);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 7, $row, $rightPoints);
                    
                    // Row styling
                    $rowRange = self::columnIndexToLetter($currentCol) . $row . ':' . self::columnIndexToLetter($currentCol + 7) . $row;
                    $rowStyle = [
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ];
                    
                    // Top 3 colors
                    if ($position === 1) {
                        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD700']];
                    } elseif ($position === 2) {
                        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0C0C0']];
                    } elseif ($position === 3) {
                        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CD7F32']];
                    }
                    
                    $sheet->getStyle($rowRange)->applyFromArray($rowStyle);
                    
                    // Red ranking number
                    $sheet->getStyle(self::columnIndexToLetter($currentCol) . $row)
                        ->getFont()->setColor(new Color('FF0000'));
                    
                    // Green Ver column
                    $sheet->getStyle(self::columnIndexToLetter($currentCol + 3) . $row)
                        ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
                    
                    $row++;
                }
                
                // Move to next column set (8 columns per match)
                $colOffset += 9; // 8 columns + 1 space
            }
        }
        
        // Set column widths
        for ($i = 0; $i < $colOffset + 8; $i++) {
            $sheet->getColumnDimensionByColumn($startCol + $i)->setWidth(12);
        }
        
        // Generate filename
        $filename = 'leaderboard-' . $kaliber . '-' . $year . '-' . now()->format('Y-m-d') . '.xlsx';
        
        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    
    // Helper function to convert column index to letter
    protected static function columnIndexToLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr($index % 26 + 65) . $letter;
            $index = intval($index / 26);
        }
        return $letter ?: 'A';
    }
    
    // Helper function to set cell value by column index
    protected static function setCellValueByColumnIndex($sheet, $colIndex, $row, $value)
    {
        $sheet->setCellValue(self::columnIndexToLetter($colIndex) . $row, $value);
    }

    protected static function downloadBestOfFiveExcelSingle($kaliber = 'KKP')
    {
        $year = date('Y');
        $kaliberLower = strtolower($kaliber);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($kaliber);
        
        // Get all matches from this year with scores for this kaliber
        $matches = Matches::whereYear('start_datum', $year)
            ->whereHas('gebruikersScores', function ($query) use ($kaliberLower) {
                $query->where('kaliber', $kaliberLower)
                      ->where('is_official', true);
            })
            ->orderBy('start_datum')
            ->get();
        
        if ($matches->isEmpty()) {
            $sheet->setCellValue('A1', 'Geen wedstrijden gevonden voor ' . $kaliber . ' in ' . $year);
            $filename = 'leaderboard-' . $kaliber . '-' . $year . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
        
        $startCol = 1;
        $colOffset = 0;
        
        // Process each match
        foreach ($matches as $match) {
            // Get scores for this match and kaliber, grouped by series
            $scores = $match->gebruikersScores()
                ->where('kaliber', $kaliberLower)
                ->where('is_official', true)
                ->with('gebruiker')
                ->get();
            
            if ($scores->isEmpty()) {
                continue;
            }
            
            $seriesGroups = $scores->groupBy('round_number');
            
            foreach ($seriesGroups as $roundNumber => $seriesScores) {
                $currentCol = $startCol + $colOffset;
                
                // Title row - Show match name and serie number
                $matchName = $match->naam ?? 'Wedstrijd';
                $titleText = "{$matchName} Serie {$roundNumber} " . ($kaliber === 'GKP' ? 'GK' : 'KK');
                self::setCellValueByColumnIndex($sheet, $currentCol, 1, $titleText);
                $sheet->mergeCells(self::columnIndexToLetter($currentCol) . '1:' . self::columnIndexToLetter($currentCol + 2) . '1');
                $sheet->getStyle(self::columnIndexToLetter($currentCol) . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Date row
                $dateText = $match->start_datum->format('d F Y');
                self::setCellValueByColumnIndex($sheet, $currentCol + 3, 1, $dateText);
                $sheet->mergeCells(self::columnIndexToLetter($currentCol + 3) . '1:' . self::columnIndexToLetter($currentCol + 6) . '1');
                $sheet->getStyle(self::columnIndexToLetter($currentCol + 3) . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FF0000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                
                // Headers
                $headers = ['Ranking', 'Schutter', 'KNSA nr', 'Ver', 'Schoten', 'totaal', 'links', 'rechts'];
                for ($i = 0; $i < count($headers); $i++) {
                    self::setCellValueByColumnIndex($sheet, $currentCol + $i, 2, $headers[$i]);
                }
                
                $headerRange = self::columnIndexToLetter($currentCol) . '2:' . self::columnIndexToLetter($currentCol + 7) . '2';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ADD8E6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // Data rows
                $sorted = $seriesScores->sortByDesc('totale_punten')->values();
                $row = 3;
                
                foreach ($sorted as $index => $score) {
                    $position = $index + 1;
                    $user = $score->gebruiker;
                    
                    // Calculate points
                    $leftPoints = ($score->linker_kaart_6 * 6) + ($score->linker_kaart_7 * 7) +
                                  ($score->linker_kaart_8 * 8) + ($score->linker_kaart_9 * 9) +
                                  ($score->linker_kaart_10 * 10);
                    
                    $rightPoints = ($score->rechter_kaart_6 * 6) + ($score->rechter_kaart_7 * 7) +
                                   ($score->rechter_kaart_8 * 8) + ($score->rechter_kaart_9 * 9) +
                                   ($score->rechter_kaart_10 * 10);
                    
                    $totalShots = ($score->linker_kaart_5 ?? 0) + ($score->linker_kaart_6 ?? 0) + 
                                  ($score->linker_kaart_7 ?? 0) + ($score->linker_kaart_8 ?? 0) + 
                                  ($score->linker_kaart_9 ?? 0) + ($score->linker_kaart_10 ?? 0) +
                                  ($score->rechter_kaart_5 ?? 0) + ($score->rechter_kaart_6 ?? 0) + 
                                  ($score->rechter_kaart_7 ?? 0) + ($score->rechter_kaart_8 ?? 0) + 
                                  ($score->rechter_kaart_9 ?? 0) + ($score->rechter_kaart_10 ?? 0);
                    
                    // Fill data
                    self::setCellValueByColumnIndex($sheet, $currentCol, $row, $position);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 1, $row, $user->avg_name ?? $user->name ?? '');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 2, $row, $user->license_number ?? '');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 3, $row, 'MO');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 4, $row, $totalShots);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 5, $row, $score->totale_punten);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 6, $row, $leftPoints);
                    self::setCellValueByColumnIndex($sheet, $currentCol + 7, $row, $rightPoints);
                    
                    // Row styling
                    $rowRange = self::columnIndexToLetter($currentCol) . $row . ':' . self::columnIndexToLetter($currentCol + 7) . $row;
                    $rowStyle = [
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ];
                    
                    // Top 3 colors
                    if ($position === 1) {
                        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD700']];
                    } elseif ($position === 2) {
                        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0C0C0']];
                    } elseif ($position === 3) {
                        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CD7F32']];
                    }
                    
                    $sheet->getStyle($rowRange)->applyFromArray($rowStyle);
                    
                    // Red ranking number
                    $sheet->getStyle(self::columnIndexToLetter($currentCol) . $row)
                        ->getFont()->setColor(new Color('FF0000'));
                    
                    // Green Ver column
                    $sheet->getStyle(self::columnIndexToLetter($currentCol + 3) . $row)
                        ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
                    
                    $row++;
                }
                
                // Move to next column set
                $colOffset += 9;
            }
        }
        
        // Set column widths
        for ($i = 0; $i < $colOffset + 8; $i++) {
            $sheet->getColumnDimensionByColumn($startCol + $i)->setWidth(12);
        }
        
        // Generate filename
        $filename = 'best-of-five-' . $kaliber . '-' . $year . '-' . now()->format('Y-m-d') . '.xlsx';
        
        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // Keep the old function for backward compatibility but now it's not used
    protected static function downloadBestOfFiveExcel()
    {
        $year = date('Y');
        
        $spreadsheet = new Spreadsheet();
        
        // Create separate sheets for KKP and GKP
        foreach (['KKP', 'GKP'] as $sheetIndex => $kaliber) {
            if ($sheetIndex > 0) {
                $sheet = $spreadsheet->createSheet();
            } else {
                $sheet = $spreadsheet->getActiveSheet();
            }
            
            $sheet->setTitle($kaliber);
            $kaliberLower = strtolower($kaliber);
            
            // Get all matches from this year with scores for this kaliber
            $matches = Matches::whereYear('start_datum', $year)
                ->whereHas('gebruikersScores', function ($query) use ($kaliberLower) {
                    $query->where('kaliber', $kaliberLower)
                          ->where('is_official', true);
                })
                ->orderBy('start_datum')
                ->get();
            
            if ($matches->isEmpty()) {
                $sheet->setCellValue('A1', 'Geen wedstrijden gevonden voor ' . $kaliber . ' in ' . $year);
                continue;
            }
            
            $startCol = 1;
            $colOffset = 0;
            
            // Process each match
            foreach ($matches as $match) {
                // Get scores for this match and kaliber, grouped by series
                $scores = $match->gebruikersScores()
                    ->where('kaliber', $kaliberLower)
                    ->where('is_official', true)
                    ->with('gebruiker')
                    ->get();
                
                if ($scores->isEmpty()) {
                    continue;
                }
                
                $seriesGroups = $scores->groupBy('round_number');
                
                foreach ($seriesGroups as $roundNumber => $seriesScores) {
                    $currentCol = $startCol + $colOffset;
                    
                    // Title row
                    $titleText = "Wedstrijd {$roundNumber} " . ($kaliber === 'GKP' ? 'GK' : 'KK');
                    self::setCellValueByColumnIndex($sheet, $currentCol, 1, $titleText);
                    $sheet->mergeCells(self::columnIndexToLetter($currentCol) . '1:' . self::columnIndexToLetter($currentCol + 2) . '1');
                    $sheet->getStyle(self::columnIndexToLetter($currentCol) . '1')->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    
                    // Date row
                    $dateText = $match->start_datum->format('d F Y');
                    self::setCellValueByColumnIndex($sheet, $currentCol + 3, 1, $dateText);
                    $sheet->mergeCells(self::columnIndexToLetter($currentCol + 3) . '1:' . self::columnIndexToLetter($currentCol + 6) . '1');
                    $sheet->getStyle(self::columnIndexToLetter($currentCol + 3) . '1')->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FF0000']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    
                    // Headers
                    $headers = ['Ranking', 'Schutter', 'KNSA nr', 'Ver', 'Schoten', 'totaal', 'links', 'rechts'];
                    for ($i = 0; $i < count($headers); $i++) {
                        self::setCellValueByColumnIndex($sheet, $currentCol + $i, 2, $headers[$i]);
                    }
                    
                    $headerRange = self::columnIndexToLetter($currentCol) . '2:' . self::columnIndexToLetter($currentCol + 7) . '2';
                    $sheet->getStyle($headerRange)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ADD8E6']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    
                    // Data rows
                    $sorted = $seriesScores->sortByDesc('totale_punten')->values();
                    $row = 3;
                    
                    foreach ($sorted as $index => $score) {
                        $position = $index + 1;
                        $user = $score->gebruiker;
                        
                        // Calculate points
                        $leftPoints = ($score->linker_kaart_6 * 6) + ($score->linker_kaart_7 * 7) +
                                      ($score->linker_kaart_8 * 8) + ($score->linker_kaart_9 * 9) +
                                      ($score->linker_kaart_10 * 10);
                        
                        $rightPoints = ($score->rechter_kaart_6 * 6) + ($score->rechter_kaart_7 * 7) +
                                       ($score->rechter_kaart_8 * 8) + ($score->rechter_kaart_9 * 9) +
                                       ($score->rechter_kaart_10 * 10);
                        
                        $totalShots = ($score->linker_kaart_5 ?? 0) + ($score->linker_kaart_6 ?? 0) + 
                                      ($score->linker_kaart_7 ?? 0) + ($score->linker_kaart_8 ?? 0) + 
                                      ($score->linker_kaart_9 ?? 0) + ($score->linker_kaart_10 ?? 0) +
                                      ($score->rechter_kaart_5 ?? 0) + ($score->rechter_kaart_6 ?? 0) + 
                                      ($score->rechter_kaart_7 ?? 0) + ($score->rechter_kaart_8 ?? 0) + 
                                      ($score->rechter_kaart_9 ?? 0) + ($score->rechter_kaart_10 ?? 0);
                        
                        // Fill data
                        self::setCellValueByColumnIndex($sheet, $currentCol, $row, $position);
                        self::setCellValueByColumnIndex($sheet, $currentCol + 1, $row, $user->avg_name ?? $user->name ?? '');
                        self::setCellValueByColumnIndex($sheet, $currentCol + 2, $row, $user->license_number ?? '');
                        self::setCellValueByColumnIndex($sheet, $currentCol + 3, $row, 'MO');
                        self::setCellValueByColumnIndex($sheet, $currentCol + 4, $row, $totalShots);
                        self::setCellValueByColumnIndex($sheet, $currentCol + 5, $row, $score->totale_punten);
                        self::setCellValueByColumnIndex($sheet, $currentCol + 6, $row, $leftPoints);
                        self::setCellValueByColumnIndex($sheet, $currentCol + 7, $row, $rightPoints);
                        
                        // Row styling
                        $rowRange = self::columnIndexToLetter($currentCol) . $row . ':' . self::columnIndexToLetter($currentCol + 7) . $row;
                        $rowStyle = [
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ];
                        
                        // Top 3 colors
                        if ($position === 1) {
                            $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD700']];
                        } elseif ($position === 2) {
                            $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0C0C0']];
                        } elseif ($position === 3) {
                            $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CD7F32']];
                        }
                        
                        $sheet->getStyle($rowRange)->applyFromArray($rowStyle);
                        
                        // Red ranking number
                        $sheet->getStyle(self::columnIndexToLetter($currentCol) . $row)
                            ->getFont()->setColor(new Color('FF0000'));
                        
                        // Green Ver column
                        $sheet->getStyle(self::columnIndexToLetter($currentCol + 3) . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
                        
                        $row++;
                    }
                    
                    // Move to next column set
                    $colOffset += 9;
                }
            }
            
            // Set column widths
            for ($i = 0; $i < $colOffset + 8; $i++) {
                $sheet->getColumnDimensionByColumn($startCol + $i)->setWidth(12);
            }
        }
        
        // Generate filename
        $filename = 'best-of-five-' . $year . '-' . now()->format('Y-m-d') . '.xlsx';
        
        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected static function generateYearLeaderboardCSV(): string
    {
        // Keep old CSV function for backward compatibility
        $year = date('Y');
        $leaderboard = self::getYearLeaderboard();

        $csv = "Algemeen Leaderboard - Jaar {$year}\n";
        $csv .= "Gegenereerd op: " . now()->format('d-m-Y H:i') . "\n\n";
        $csv .= "Positie,Kaliber,Naam,Totaal Punten,Gemiddeld,Beste Score,Aantal Series\n";

        foreach ($leaderboard as $entry) {
            $csv .= "{$entry['position']},{$entry['kaliber']},{$entry['name']},{$entry['total_points']},{$entry['average_points']},{$entry['best_score']},{$entry['series_count']}\n";
        }

        return $csv;
    }
}

