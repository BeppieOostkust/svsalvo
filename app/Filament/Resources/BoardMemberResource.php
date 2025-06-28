<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoardMemberResource\Pages;
use App\Models\BoardMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BooleanColumn;

class BoardMemberResource extends Resource
{
    protected static ?string $model = BoardMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Bestuur';
    
    protected static ?string $modelLabel = 'Bestuurslid';
    
    protected static ?string $pluralModelLabel = 'Bestuursleden';
    
    protected static ?string $navigationGroup = 'Organisatie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full'),
                    
                TextInput::make('position')
                    ->label('Functie')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('bijv. Voorzitter, Secretaris, Penningmeester')
                    ->columnSpan('full'),
                    
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                    
                TextInput::make('phone')
                    ->label('Telefoon')
                    ->tel()
                    ->maxLength(255),
                    
                Textarea::make('description')
                    ->label('Beschrijving')
                    ->rows(3)
                    ->columnSpan('full'),
                    
                FileUpload::make('avatar')
                    ->label('Profielfoto')
                    ->image()
                    ->directory('board-members')
                    ->visibility('public')
                    ->columnSpan('full'),
                    
                TextInput::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->numeric()
                    ->default(0)
                    ->required(),
                    
                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(50),
                    
                TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('position')
                    ->label('Functie')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('E-mail gekopieerd!')
                    ->icon('heroicon-m-envelope'),
                    
                TextColumn::make('phone')
                    ->label('Telefoon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Telefoonnummer gekopieerd!')
                    ->icon('heroicon-m-phone'),
                    
                TextColumn::make('sort_order')
                    ->label('Volgorde')
                    ->sortable(),
                    
                BooleanColumn::make('is_active')
                    ->label('Actief'),
                    
                TextColumn::make('updated_at')
                    ->label('Laatst Bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Alleen actieve bestuursleden')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Bewerken'),
                Tables\Actions\DeleteAction::make()
                    ->label('Verwijderen'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Verwijderen'),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoardMembers::route('/'),
            'create' => Pages\CreateBoardMember::route('/create'),
            'edit' => Pages\EditBoardMember::route('/{record}/edit'),
        ];
    }
}
