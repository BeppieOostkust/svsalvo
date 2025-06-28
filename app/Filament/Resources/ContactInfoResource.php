<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactInfoResource\Pages;
use App\Models\ContactInfo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class ContactInfoResource extends Resource
{
    protected static ?string $model = ContactInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Contactinformatie';
    
    protected static ?string $modelLabel = 'Contactinformatie';
    
    protected static ?string $pluralModelLabel = 'Contactinformatie';
    
    protected static ?string $navigationGroup = 'Organisatie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label('Type')
                    ->required()
                    ->options([
                        'address' => 'Adres',
                        'contact' => 'Contact',
                        'opening_hours' => 'Openingstijden',
                        'other' => 'Overig',
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('data', null)),
                    
                TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255),
                    
                // Dynamic fields based on type
                Forms\Components\Group::make()
                    ->schema(fn (Get $get): array => match ($get('type')) {
                        'address' => [
                            TextInput::make('data.street')
                                ->label('Straat + Huisnummer')
                                ->required(fn (Get $get): bool => $get('type') === 'address')
                                ->maxLength(255),
                            TextInput::make('data.postal_code')
                                ->label('Postcode')
                                ->required(fn (Get $get): bool => $get('type') === 'address')
                                ->maxLength(10),
                            TextInput::make('data.city')
                                ->label('Stad')
                                ->required(fn (Get $get): bool => $get('type') === 'address')
                                ->maxLength(255),
                            TextInput::make('data.country')
                                ->label('Land')
                                ->default('Nederland')
                                ->maxLength(255),
                            TextInput::make('data.google_maps_url')
                                ->label('Google Maps URL')
                                ->url()
                                ->placeholder('https://maps.google.com/...')
                                ->helperText('Plak hier de Google Maps link voor dit adres')
                                ->columnSpanFull(),
                            Forms\Components\Group::make([
                                TextInput::make('data.latitude')
                                    ->label('Breedtegraad (Latitude)')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->helperText('Optioneel: voor nauwkeurige kaartweergave'),
                                TextInput::make('data.longitude')
                                    ->label('Lengtegraad (Longitude)')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->helperText('Optioneel: voor nauwkeurige kaartweergave'),
                            ])->columns(2),
                        ],
                        'contact' => [
                            TextInput::make('data.email')
                                ->label('E-mail')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('data.phone')
                                ->label('Telefoon')
                                ->tel()
                                ->maxLength(255),
                            TextInput::make('data.website')
                                ->label('Website')
                                ->url()
                                ->maxLength(255),
                        ],
                        'opening_hours' => [
                            Forms\Components\Repeater::make('data.hours')
                                ->label('Openingstijden')
                                ->schema([
                                    Select::make('day')
                                        ->label('Dag')
                                        ->options([
                                            'Maandag' => 'Maandag',
                                            'Dinsdag' => 'Dinsdag',
                                            'Woensdag' => 'Woensdag',
                                            'Donderdag' => 'Donderdag',
                                            'Vrijdag' => 'Vrijdag',
                                            'Zaterdag' => 'Zaterdag',
                                            'Zondag' => 'Zondag',
                                        ])
                                        ->required(),
                                    TextInput::make('hours')
                                        ->label('Tijden')
                                        ->placeholder('bijv. 19:00 - 22:00 of Gesloten')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel('Dag toevoegen'),
                        ],
                        default => [
                            Textarea::make('data')
                                ->label('Data (JSON of tekst)')
                                ->rows(3)
                                ->required(),
                        ],
                    })
                    ->columnSpanFull(),
                    
                Textarea::make('additional_info')
                    ->label('Extra informatie')
                    ->rows(2)
                    ->columnSpanFull(),
                    
                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'address' => 'primary',
                        'contact' => 'success',
                        'opening_hours' => 'warning',
                        'other' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable(),
                TextColumn::make('address_summary')
                    ->label('Samenvatting')
                    ->getStateUsing(function (ContactInfo $record): string {
                        if ($record->type === 'address' && is_array($record->data)) {
                            return ($record->data['street'] ?? '') . ', ' . ($record->data['city'] ?? '');
                        }
                        if ($record->type === 'contact' && is_array($record->data)) {
                            return ($record->data['email'] ?? '') . ' | ' . ($record->data['phone'] ?? '');
                        }
                        return 'Klik om te bekijken';
                    })
                    ->limit(50),
                BooleanColumn::make('is_active')
                    ->label('Actief'),
                TextColumn::make('updated_at')
                    ->label('Laatst Bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'address' => 'Adres',
                        'contact' => 'Contact',
                        'opening_hours' => 'Openingstijden',
                        'other' => 'Overig',
                    ]),
                Tables\Filters\Filter::make('is_active')
                    ->label('Alleen actieve')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactInfos::route('/'),
            'create' => Pages\CreateContactInfo::route('/create'),
            'edit' => Pages\EditContactInfo::route('/{record}/edit'),
        ];
    }
}
