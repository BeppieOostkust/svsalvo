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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('naam'),
                Forms\Components\TextInput::make(name: 'beschrijving'),
                Forms\Components\Select::make('status')->options([
                    'binnenkort' => 'Binnenkort',
                    'bezig' => 'Bezig',
                    'geannuleerd' => 'Geannuleerd',
                    'afgelopen' => 'Afgelopen',
                ]),
                Forms\Components\DateTimePicker::make('start_datum')
                    ->label('Start Datum')
                    ->required()
                    ->default(now())
                    ->displayFormat('d-m-Y H:i:s')
                    ->seconds(false)
                    ->timezone('Europe/Amsterdam'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_datum')
                    ->label('Startdatum')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('beschrijving')
                    ->label('Description')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'binnenkort' => 'warning',
                        'bezig' => 'success',
                        'geannuleerd' => 'danger',
                        'afgelopen' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Gecreëerd op')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Laatst bijgewerkt op')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make()->modalHeading('Verander wedstrijd')->modalButton('Wijzigingen opslaan')->modalWidth('xl'),
                Tables\Actions\Action::make('exportScores')
                    ->label('Export Scores')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Matches $record) {
                        try {
                            $exportService = new \App\Services\ScoreExportService();
                            $filePath = $exportService->exportMatchScores($record);
                            $fileName = 'wedstrijd_scores_' . $record->naam . '_' . date('Y-m-d') . '.xlsx';
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Export Succesvol')
                                ->body('Scores zijn geëxporteerd naar Excel.')
                                ->success()
                                ->send();
                            
                            return response()->download($filePath, $fileName)->deleteFileAfterSend();
                            
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Export Mislukt')
                                ->body('Er is een fout opgetreden: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
                $query->orderByDesc('totale_punten')
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
}

