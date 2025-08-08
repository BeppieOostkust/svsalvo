<?php

namespace App\Filament\Actions;

use App\Models\Matches;
use App\Services\ScoreExportService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportScoresAction
{
    public static function make(): \Filament\Tables\Actions\Action
    {
        return \Filament\Tables\Actions\Action::make('exportScores')
            ->label('Export Scores')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function (Matches $record) {
                $exportService = new ScoreExportService();
                
                try {
                    $filePath = $exportService->exportMatchScores($record);
                    $fileName = 'wedstrijd_scores_' . $record->naam . '_' . date('Y-m-d') . '.xlsx';
                    
                    Notification::make()
                        ->title('Export Succesvol')
                        ->body('Scores zijn geëxporteerd naar Excel.')
                        ->success()
                        ->send();
                    
                    return response()->download($filePath, $fileName)->deleteFileAfterSend();
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Export Mislukt')
                        ->body('Er is een fout opgetreden bij het exporteren: ' . $e->getMessage())
                        ->danger()
                        ->send();
                    
                    return null;
                }
            });
    }

    public static function makeBulk(): \Filament\Tables\Actions\BulkAction
    {
        return \Filament\Tables\Actions\BulkAction::make('exportSelectedScores')
            ->label('Export Geselecteerde')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function ($records) {
                $exportService = new ScoreExportService();
                
                try {
                    // For multiple matches, create a combined export
                    $filePath = static::exportMultipleMatches($records, $exportService);
                    $fileName = 'wedstrijden_scores_' . date('Y-m-d') . '.xlsx';
                    
                    Notification::make()
                        ->title('Bulk Export Succesvol')
                        ->body(count($records) . ' wedstrijden zijn geëxporteerd naar Excel.')
                        ->success()
                        ->send();
                    
                    return response()->download($filePath, $fileName)->deleteFileAfterSend();
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Bulk Export Mislukt')
                        ->body('Er is een fout opgetreden bij het exporteren: ' . $e->getMessage())
                        ->danger()
                        ->send();
                    
                    return null;
                }
            });
    }

    public static function makeGlobalExport(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('exportAllScores')
            ->label('Export Alle Scores')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Export Alle Scores')
            ->modalDescription('Dit zal alle officiële scores van alle wedstrijden exporteren. Dit kan even duren.')
            ->modalSubmitActionLabel('Ja, Exporteer Alles')
            ->action(function () {
                $exportService = new ScoreExportService();
                
                try {
                    $filePath = $exportService->exportAllScores();
                    $fileName = 'alle_scores_complete_export_' . date('Y-m-d') . '.xlsx';
                    
                    Notification::make()
                        ->title('Complete Export Succesvol')
                        ->body('Alle scores zijn geëxporteerd naar Excel met leaderboards per discipline.')
                        ->success()
                        ->send();
                    
                    return response()->download($filePath, $fileName)->deleteFileAfterSend();
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Complete Export Mislukt')
                        ->body('Er is een fout opgetreden bij het exporteren: ' . $e->getMessage())
                        ->danger()
                        ->send();
                    
                    return null;
                }
            });
    }

    private static function exportMultipleMatches($matches, ScoreExportService $exportService): string
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default sheet
        
        $sheetIndex = 0;
        foreach ($matches as $match) {
            // Get scores for this match
            $scores = \App\Models\MatchGebruikerScore::where('wedstrijd_id', $match->id)
                ->where('is_official', true)
                ->with(['user'])
                ->orderBy('kaliber')
                ->orderByDesc('totale_punten')
                ->get();

            if ($scores->isEmpty()) {
                continue; // Skip matches with no official scores
            }

            // Create sheet for this match
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($match->naam, 0, 31)); // Excel sheet name limit
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            
            // Use the same logic as single match export but in a sheet
            static::populateMatchSheet($sheet, $match, $scores);
            $sheetIndex++;
        }

        // If no sheets were created, create a summary sheet
        if ($sheetIndex === 0) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Geen Data');
            $sheet->setCellValue('A1', 'Geen officiële scores gevonden in de geselecteerde wedstrijden.');
        } else {
            // Set first sheet as active
            $spreadsheet->setActiveSheetIndex(0);
        }

        // Save to temporary file
        $fileName = 'bulk_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    private static function populateMatchSheet($sheet, $match, $scores): void
    {
        // Title
        $sheet->setCellValue('A1', 'Wedstrijd: ' . $match->naam);
        $sheet->setCellValue('A2', 'Datum: ' . $match->start_datum->format('d-m-Y H:i'));
        
        $currentRow = 4;
        
        // Group by discipline
        $scoresByDiscipline = $scores->groupBy('kaliber');
        
        foreach ($scoresByDiscipline as $discipline => $disciplineScores) {
            // Discipline header
            $sheet->setCellValue('A' . $currentRow, strtoupper($discipline));
            $currentRow += 2;

            // Headers
            $headers = ['Pos.', 'Naam', 'Email', 'Totaal', 'L6', 'L7', 'L8', 'L9', 'L10', 'R6', 'R7', 'R8', 'R9', 'R10'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $currentRow, $header);
                $col++;
            }
            $currentRow++;

            // Data
            $position = 1;
            foreach ($disciplineScores as $score) {
                $user = $score->user;
                $userName = $user->first_name && $user->last_name 
                    ? $user->first_name . ' ' . $user->last_name 
                    : $user->name;

                $sheet->setCellValue('A' . $currentRow, $position);
                $sheet->setCellValue('B' . $currentRow, $userName);
                $sheet->setCellValue('C' . $currentRow, $user->email);
                $sheet->setCellValue('D' . $currentRow, $score->totale_punten ?? 0);
                $sheet->setCellValue('E' . $currentRow, $score->linker_kaart_6 ?? 0);
                $sheet->setCellValue('F' . $currentRow, $score->linker_kaart_7 ?? 0);
                $sheet->setCellValue('G' . $currentRow, $score->linker_kaart_8 ?? 0);
                $sheet->setCellValue('H' . $currentRow, $score->linker_kaart_9 ?? 0);
                $sheet->setCellValue('I' . $currentRow, $score->linker_kaart_10 ?? 0);
                $sheet->setCellValue('J' . $currentRow, $score->rechter_kaart_6 ?? 0);
                $sheet->setCellValue('K' . $currentRow, $score->rechter_kaart_7 ?? 0);
                $sheet->setCellValue('L' . $currentRow, $score->rechter_kaart_8 ?? 0);
                $sheet->setCellValue('M' . $currentRow, $score->rechter_kaart_9 ?? 0);
                $sheet->setCellValue('N' . $currentRow, $score->rechter_kaart_10 ?? 0);

                $currentRow++;
                $position++;
            }
            
            $currentRow += 2; // Space between disciplines
        }

        // Auto-size columns
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
