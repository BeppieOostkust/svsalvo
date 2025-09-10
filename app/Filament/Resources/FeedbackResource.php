<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Filament\Resources\FeedbackResource\RelationManagers;
use App\Models\Feedback;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Feedback & Suggesties';

    protected static ?string $modelLabel = 'Feedback';

    protected static ?string $pluralModelLabel = 'Feedback & Suggesties';

    protected static ?string $navigationGroup = 'Content Beheer';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Beschrijving')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'idea' => 'Idee',
                                        'feedback' => 'Feedback',
                                        'suggestion' => 'Suggestie',
                                        'bug_report' => 'Bug Report',
                                        'feature_request' => 'Feature Verzoek',
                                    ])
                                    ->required(),
                                
                                Forms\Components\Select::make('priority')
                                    ->label('Prioriteit')
                                    ->options([
                                        'low' => 'Laag',
                                        'medium' => 'Middel',
                                        'high' => 'Hoog',
                                        'urgent' => 'Urgent',
                                    ])
                                    ->default('medium')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Status & Moderatie')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'In afwachting',
                                'under_review' => 'In behandeling',
                                'approved' => 'Goedgekeurd',
                                'rejected' => 'Afgewezen',
                                'implemented' => 'Geïmplementeerd',
                                'closed' => 'Gesloten',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Forms\Components\Textarea::make('moderator_notes')
                            ->label('Moderator Notities')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('admin_response')
                            ->label('Admin Reactie')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('moderator_id')
                                    ->label('Toegewezen Moderator')
                                    ->options(User::where('is_admin', true)->pluck('name', 'id'))
                                    ->searchable(),
                                
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Uitgelicht'),
                            ]),
                    ]),

                Forms\Components\Section::make('Gebruiker & Instellingen')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Gebruiker')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                        
                        Forms\Components\Toggle::make('is_anonymous')
                            ->label('Anoniem'),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('upvotes')
                                    ->label('Upvotes')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
                                
                                Forms\Components\TextInput::make('downvotes')
                                    ->label('Downvotes')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'idea' => 'Idee',
                        'feedback' => 'Feedback',
                        'suggestion' => 'Suggestie',
                        'bug_report' => 'Bug Report',
                        'feature_request' => 'Feature Verzoek',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'idea',
                        'success' => 'feedback',
                        'warning' => 'suggestion',
                        'danger' => 'bug_report',
                        'info' => 'feature_request',
                    ]),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'In afwachting',
                        'under_review' => 'In behandeling',
                        'approved' => 'Goedgekeurd',
                        'rejected' => 'Afgewezen',
                        'implemented' => 'Geïmplementeerd',
                        'closed' => 'Gesloten',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'implemented',
                        'gray' => 'closed',
                    ]),
                
                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Prioriteit')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Laag',
                        'medium' => 'Middel',
                        'high' => 'Hoog',
                        'urgent' => 'Urgent',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'purple' => 'urgent',
                    ]),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Gebruiker')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('moderator.name')
                    ->label('Moderator')
                    ->sortable()
                    ->placeholder('Niet toegewezen'),
                
                Tables\Columns\TextColumn::make('net_votes')
                    ->label('Stemmen')
                    ->getStateUsing(fn (Feedback $record): string => 
                        '+' . $record->upvotes . ' / -' . $record->downvotes . ' (net: ' . ($record->upvotes - $record->downvotes) . ')'
                    )
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("(upvotes - downvotes) {$direction}");
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Uitgelicht')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Beoordeeld')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nog niet beoordeeld'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'idea' => 'Idee',
                        'feedback' => 'Feedback',
                        'suggestion' => 'Suggestie',
                        'bug_report' => 'Bug Report',
                        'feature_request' => 'Feature Verzoek',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'In afwachting',
                        'under_review' => 'In behandeling',
                        'approved' => 'Goedgekeurd',
                        'rejected' => 'Afgewezen',
                        'implemented' => 'Geïmplementeerd',
                        'closed' => 'Gesloten',
                    ]),
                
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioriteit')
                    ->options([
                        'low' => 'Laag',
                        'medium' => 'Middel',
                        'high' => 'Hoog',
                        'urgent' => 'Urgent',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Uitgelicht'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Goedkeuren')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Feedback $record) {
                            $record->update([
                                'status' => 'approved',
                                'moderator_id' => Auth::id(),
                                'reviewed_at' => now(),
                            ]);
                        })
                        ->visible(fn (Feedback $record): bool => $record->status === 'pending'),
                    
                    Tables\Actions\Action::make('reject')
                        ->label('Afwijzen')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Feedback $record) {
                            $record->update([
                                'status' => 'rejected',
                                'moderator_id' => Auth::id(),
                                'reviewed_at' => now(),
                            ]);
                        })
                        ->visible(fn (Feedback $record): bool => $record->status === 'pending'),
                    
                    Tables\Actions\Action::make('feature')
                        ->label('Uitlichten')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->action(function (Feedback $record) {
                            $record->update(['is_featured' => !$record->is_featured]);
                        }),
                    
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Goedkeuren')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function (Feedback $record) {
                                $record->update([
                                    'status' => 'approved',
                                    'moderator_id' => Auth::id(),
                                    'reviewed_at' => now(),
                                ]);
                            });
                        }),
                    
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Afwijzen')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function (Feedback $record) {
                                $record->update([
                                    'status' => 'rejected',
                                    'moderator_id' => Auth::id(),
                                    'reviewed_at' => now(),
                                ]);
                            });
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
