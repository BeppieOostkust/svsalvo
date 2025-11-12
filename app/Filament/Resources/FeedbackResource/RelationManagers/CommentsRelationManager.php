<?php

namespace App\Filament\Resources\FeedbackResource\RelationManagers;

use App\Services\EmailService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->label('Reactie')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Gebruiker')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Reactie')
                    ->limit(50)
                    ->html(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Datum')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    })
                    ->after(function (Model $record) {
                        // Send email to feedback creator
                        $feedback = $this->getOwnerRecord();
                        
                        if ($feedback->user && $feedback->user->email) {
                            $emailService = new EmailService();
                            $emailSent = $emailService->sendFeedbackResponseEmail(
                                $feedback->user,
                                $feedback,
                                strip_tags($record->content)
                            );

                            if ($emailSent) {
                                Notification::make()
                                    ->title('Reactie toegevoegd en email verzonden')
                                    ->body("✅ Email verzonden naar {$feedback->user->name}")
                                    ->success()
                                    ->send();
                            }
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
