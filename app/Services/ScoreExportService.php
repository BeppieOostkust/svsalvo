<?php

namespace App\Services;

use App\Models\Matches;
use App\Models\MatchGebruikerScore;
use App\Models\User;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ScoreExportService
{
    public function exportMatchScores(Matches $match): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Get only official scores
        $scores = MatchGebruikerScore::where('wedstrijd_id', $match->id)
            ->where('is_official', true)
            ->with(['user'])
            ->orderBy('kaliber')
            ->orderByDesc('totale_punten')
            ->get();

        // Group scores by discipline (kaliber)
        $scoresByDiscipline = $scores->groupBy('kaliber');

        $currentRow = 1;
        
        // Title
        $sheet->setCellValue('A1', 'Wedstrijd Scores Export: ' . $match->naam);
        $sheet->mergeCells('A1:O1');
        $this->styleHeader($sheet, 'A1:O1');
        $currentRow += 2;

        // Match info
        $sheet->setCellValue('A3', 'Wedstrijd: ' . $match->naam);
        $sheet->setCellValue('A4', 'Datum: ' . $match->start_datum->format('d-m-Y H:i'));
        $sheet->setCellValue('A5', 'Status: ' . $match->status);
        $currentRow = 7;

        foreach ($scoresByDiscipline as $discipline => $disciplineScores) {
            $currentRow = $this->addDisciplineSection($sheet, $discipline, $disciplineScores, $currentRow);
            $currentRow += 2; // Add space between disciplines
        }

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Save to temporary file
        $fileName = 'wedstrijd_scores_' . $match->id . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    public function exportAllScores(): string
    {
        $spreadsheet = new Spreadsheet();
        
        // Get all disciplines
        $disciplines = MatchGebruikerScore::where('is_official', true)
            ->distinct()
            ->pluck('kaliber')
            ->filter()
            ->sort();

        $sheetIndex = 0;
        foreach ($disciplines as $discipline) {
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle($this->getDisciplineName($discipline));
            
            $this->createDisciplineLeaderboard($sheet, $discipline);
            $sheetIndex++;
        }

        // Create summary sheet
        $spreadsheet->createSheet();
        $summarySheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
        $summarySheet->setTitle('Samenvatting');
        $this->createSummarySheet($summarySheet);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Save to temporary file
        $fileName = 'alle_scores_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    private function addDisciplineSection($sheet, string $discipline, Collection $scores, int $startRow): int
    {
        $currentRow = $startRow;
        
        // Discipline header
        $sheet->setCellValue('A' . $currentRow, strtoupper($this->getDisciplineName($discipline)));
        $sheet->mergeCells('A' . $currentRow . ':O' . $currentRow);
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':O' . $currentRow);
        $currentRow += 2;

        // Table headers
        $headers = [
            'A' => 'Pos.',
            'B' => 'Naam',
            'C' => 'Email',
            'D' => 'Totaal',
            'E' => 'L6',
            'F' => 'L7',
            'G' => 'L8',
            'H' => 'L9',
            'I' => 'L10',
            'J' => 'R6',
            'K' => 'R7',
            'L' => 'R8',
            'M' => 'R9',
            'N' => 'R10',
            'O' => 'Ronde'
        ];

        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':O' . $currentRow);
        $currentRow++;

        // Scores data
        $position = 1;
        foreach ($scores as $score) {
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
            $sheet->setCellValue('O' . $currentRow, $score->round_number ?? 1);

            $this->styleTableRow($sheet, 'A' . $currentRow . ':O' . $currentRow, $position % 2 == 0);
            $currentRow++;
            $position++;
        }

        return $currentRow;
    }

    private function createDisciplineLeaderboard($sheet, string $discipline): void
    {
        // Get aggregated scores for this discipline
        $users = User::whereHas('matchScores', function ($query) use ($discipline) {
            $query->where('kaliber', $discipline)
                  ->where('is_official', true);
        })
        ->with(['matchScores' => function ($query) use ($discipline) {
            $query->where('kaliber', $discipline)
                  ->where('is_official', true);
        }])
        ->get()
        ->map(function ($user) {
            $scores = $user->matchScores;
            $totalScore = $scores->sum('totale_punten');
            $matchCount = $scores->count();
            $averageScore = $matchCount > 0 ? $totalScore / $matchCount : 0;

            return [
                'user' => $user,
                'total_score' => $totalScore,
                'average_score' => $averageScore,
                'match_count' => $matchCount,
                'best_score' => $scores->max('totale_punten') ?? 0,
            ];
        })
        ->sortByDesc('total_score')
        ->values();

        // Title
        $sheet->setCellValue('A1', 'Leaderboard: ' . $this->getDisciplineName($discipline));
        $sheet->mergeCells('A1:G1');
        $this->styleHeader($sheet, 'A1:G1');

        // Headers
        $headers = [
            'A' => 'Positie',
            'B' => 'Naam',
            'C' => 'Email',
            'D' => 'Totaal Score',
            'E' => 'Gemiddelde',
            'F' => 'Beste Score',
            'G' => 'Aantal Wedstrijden'
        ];

        $headerRow = 3;
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $headerRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $headerRow . ':G' . $headerRow);

        // Data
        $currentRow = 4;
        $position = 1;
        foreach ($users as $userData) {
            $user = $userData['user'];
            $userName = $user->first_name && $user->last_name 
                ? $user->first_name . ' ' . $user->last_name 
                : $user->name;

            $sheet->setCellValue('A' . $currentRow, $position);
            $sheet->setCellValue('B' . $currentRow, $userName);
            $sheet->setCellValue('C' . $currentRow, $user->email);
            $sheet->setCellValue('D' . $currentRow, $userData['total_score']);
            $sheet->setCellValue('E' . $currentRow, round($userData['average_score'], 1));
            $sheet->setCellValue('F' . $currentRow, $userData['best_score']);
            $sheet->setCellValue('G' . $currentRow, $userData['match_count']);

            $this->styleTableRow($sheet, 'A' . $currentRow . ':G' . $currentRow, $position % 2 == 0);
            $currentRow++;
            $position++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function createSummarySheet($sheet): void
    {
        $sheet->setCellValue('A1', 'Score Export Samenvatting');
        $sheet->mergeCells('A1:D1');
        $this->styleHeader($sheet, 'A1:D1');

        $currentRow = 3;

        // Overall statistics
        $totalMatches = Matches::count();
        $totalOfficialScores = MatchGebruikerScore::where('is_official', true)->count();
        $totalUsers = User::whereHas('matchScores', function ($query) {
            $query->where('is_official', true);
        })->count();

        $stats = [
            'Totaal aantal wedstrijden' => $totalMatches,
            'Totaal aantal officiële scores' => $totalOfficialScores,
            'Aantal actieve schutters' => $totalUsers,
            'Export datum' => date('d-m-Y H:i:s')
        ];

        foreach ($stats as $label => $value) {
            $sheet->setCellValue('A' . $currentRow, $label);
            $sheet->setCellValue('B' . $currentRow, $value);
            $currentRow++;
        }

        // Discipline breakdown
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'Scores per Discipline');
        $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':C' . $currentRow);
        $currentRow += 2;

        $sheet->setCellValue('A' . $currentRow, 'Discipline');
        $sheet->setCellValue('B' . $currentRow, 'Aantal Scores');
        $sheet->setCellValue('C' . $currentRow, 'Aantal Schutters');
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':C' . $currentRow);
        $currentRow++;

        $disciplineStats = MatchGebruikerScore::where('is_official', true)
            ->selectRaw('kaliber, COUNT(*) as score_count, COUNT(DISTINCT gebruiker_id) as user_count')
            ->whereNotNull('kaliber')
            ->groupBy('kaliber')
            ->get();

        foreach ($disciplineStats as $stat) {
            $sheet->setCellValue('A' . $currentRow, $this->getDisciplineName($stat->kaliber));
            $sheet->setCellValue('B' . $currentRow, $stat->score_count);
            $sheet->setCellValue('C' . $currentRow, $stat->user_count);
            $this->styleTableRow($sheet, 'A' . $currentRow . ':C' . $currentRow, $currentRow % 2 == 0);
            $currentRow++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function getDisciplineName(string $discipline): string
    {
        $names = [
            'gkp' => 'Grote Kaliber Pistool',
            'kkp' => 'Kleine Kaliber Pistool',
            'gkg' => 'Grote Kaliber Geweer',
            'kkg' => 'Kleine Kaliber Geweer',
            'luchtpistool' => 'Luchtpistool',
            'luchtwapen' => 'Luchtwapen',
        ];

        return $names[$discipline] ?? ucfirst($discipline);
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
    }

    private function styleSubHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6366F1']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
    }

    private function styleTableHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8B5CF6']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);
    }

    private function styleTableRow($sheet, string $range, bool $isEven): void
    {
        $fillColor = $isEven ? 'F8FAFC' : 'FFFFFF';
        
        $sheet->getStyle($range)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $fillColor]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
    }
}
