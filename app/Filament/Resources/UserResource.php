<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('avg_name')
                            ->required()
                            ->label('AVG Ledennaam'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Weergavenaam'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Email'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Wachtwoord')
                            ->helperText('Laat leeg om het huidige wachtwoord te behouden (alleen bij bewerken)'),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Administrator'),
                        Forms\Components\Toggle::make('is_active_member')
                            ->label('Actief lid')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Persoonlijke Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Voornaam'),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Achternaam'),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Geboortedatum'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefoon'),
                        Forms\Components\Textarea::make('address')
                            ->label('Adres'),
                        Forms\Components\TextInput::make('city')
                            ->label('Stad'),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Postcode'),
                        Forms\Components\TextInput::make('country')
                            ->label('Land')
                            ->default('Nederland'),
                    ])->columns(2),

                Forms\Components\Section::make('Vereniging Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('Functie'),
                        Forms\Components\Textarea::make('bio')
                            ->label('Biografie'),
                        Forms\Components\FileUpload::make('profile_image')
                            ->label('Profielfoto')
                            ->image()
                            ->disk('public')
                            ->directory('profile-images')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5120)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400'),
                        Forms\Components\Toggle::make('show_in_organization')
                            ->label('Toon in organisatie overzicht'),
                        Forms\Components\TextInput::make('organization_sort_order')
                            ->label('Volgorde in organisatie')
                            ->numeric()
                            ->default(0),
                        Forms\Components\DatePicker::make('member_since')
                            ->label('Lid sinds'),
                    ])->columns(2),

                Forms\Components\Section::make('Schiet Informatie')
                    ->schema([
                        Forms\Components\Select::make('preferred_discipline')
                            ->label('Voorkeur discipline')
                            ->options([
                                'gkp' => 'GKP',
                                'kkp' => 'KKP',
                                'gkg' => 'GKG',
                                'kkg' => 'KKG',
                                'luchtpistool' => 'Luchtpistool',
                                'luchtwapen' => 'Luchtwapen',
                            ]),
                        Forms\Components\TextInput::make('license_number')
                            ->label('Licentienummer'),
                        Forms\Components\DatePicker::make('license_expiry')
                            ->label('Licentie verloopt'),
                    ])->columns(2),

                Forms\Components\Section::make('Privacy Instellingen')
                    ->schema([
                        Forms\Components\Toggle::make('show_contact_info')
                            ->label('Contactgegevens openbaar'),
                        Forms\Components\Toggle::make('show_scores_public')
                            ->label('Scores openbaar')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'avg_name')
                    ->label('AVG Ledennaam')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Weergavenaam')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Volledige naam')
                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active_member')
                    ->label('Actief lid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Functie')
                    ->searchable(),
                Tables\Columns\TextColumn::make('preferred_discipline')
                    ->label('Discipline')
                    ->badge(),
                Tables\Columns\TextColumn::make('member_since')
                    ->label('Lid sinds')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Account aangemaakt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Administrators'),
                Tables\Filters\TernaryFilter::make('is_active_member')
                    ->label('Actieve leden'),
                Tables\Filters\TernaryFilter::make('show_in_organization')
                    ->label('Zichtbaar in organisatie'),
                Tables\Filters\SelectFilter::make('preferred_discipline')
                    ->label('Discipline')
                    ->options([
                        'gkp' => 'GKP',
                        'kkp' => 'KKP',
                        'gkg' => 'GKG',
                        'kkg' => 'KKG',
                        'luchtpistool' => 'Luchtpistool',
                        'luchtwapen' => 'Luchtwapen',
                    ]),
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

    public static function getNavigationLabel(): string
    {
        return 'Leden';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leden Beheer';
    }

    public static function getModelLabel(): string
    {
        return 'Lid';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leden';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
