<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = 'Nieuws';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $modelLabel = 'artikel';
    
    protected static ?string $pluralModelLabel = 'artikelen';

    protected static ?string $navigationGroup = 'Content Beheer';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Artikel Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                            ),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Concept',
                                'published' => 'Gepubliceerd',
                                'archived' => 'Gearchiveerd',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->required()
                            ->default(auth()->id()),
                    ])
                    ->columns(2),
                    
                Section::make('Inhoud')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Samenvatting')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Korte samenvatting die wordt getoond op overzichtspagina\'s'),
                        Forms\Components\RichEditor::make('content')
                            ->label('Inhoud')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'h2',
                                'h3',
                            ]),
                    ]),
                    
                Section::make('Media & Publicatie')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Hoofdafbeelding')
                            ->image()
                            ->directory('articles')
                            ->imageEditor(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publicatiedatum')
                            ->default(now())
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Uitgelicht artikel')
                            ->default(false),
                        Forms\Components\Toggle::make('is_urgent')
                            ->label('Urgent artikel')
                            ->helperText('Urgent artikelen worden op elke pagina getoond als waarschuwing')
                            ->default(false),
                        Forms\Components\Toggle::make('allow_comments')
                            ->label('Reacties toestaan')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Afbeelding')
                    ->circular()
                    ->defaultImageUrl('/images/placeholder.png'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Concept',
                        'published' => 'Gepubliceerd',
                        'archived' => 'Gearchiveerd',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Auteur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Gepubliceerd op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Uitgelicht')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_urgent')
                    ->label('Urgent')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Reacties')
                    ->counts('comments')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Concept',
                        'published' => 'Gepubliceerd',
                        'archived' => 'Gearchiveerd',
                    ]),
                SelectFilter::make('author')
                    ->label('Auteur')
                    ->relationship('author', 'name'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Uitgelicht')
                    ->placeholder('Alle artikelen')
                    ->trueLabel('Alleen uitgelichte artikelen')
                    ->falseLabel('Niet uitgelichte artikelen'),
                Tables\Filters\TernaryFilter::make('is_urgent')
                    ->label('Urgent')
                    ->placeholder('Alle artikelen')
                    ->trueLabel('Alleen urgente artikelen')
                    ->falseLabel('Niet urgente artikelen'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Bekijken'),
                Tables\Actions\EditAction::make()
                    ->label('Bewerken'),
                Tables\Actions\DeleteAction::make()
                    ->label('Verwijderen'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Geselecteerde verwijderen'),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Algemene Beheer';
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
