<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBlockActionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('is_blocked', true)
                    ->whereNotNull('blocked_at')
                    ->orderBy('blocked_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Gebruiker')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('blocked_reason')
                    ->label('Reden')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->blocked_reason)
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('blocked_at')
                    ->label('Geblokkeerd op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('unblock')
                    ->label('Deblokkeren')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'is_blocked' => false,
                            'blocked_reason' => null,
                            'blocked_at' => null,
                        ]);
                        
                        $this->dispatch('$refresh');
                    }),
            ])
            ->heading('Recent Geblokkeerde Gebruikers')
            ->description('Laatste 10 geblokkeerde gebruikers')
            ->emptyStateHeading('Geen geblokkeerde gebruikers')
            ->emptyStateDescription('Er zijn momenteel geen geblokkeerde gebruikers.')
            ->emptyStateIcon('heroicon-o-lock-open');
    }
}
