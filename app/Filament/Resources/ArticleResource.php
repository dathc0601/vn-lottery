<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('article.articles.label');
    }

    public static function getModelLabel(): string
    {
        return __('article.articles.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('article.articles.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('article.sections.basic'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(500)
                                    ->label(__('article.fields.title'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Forms\Set $set, ?string $old) {
                                        if (($old ?? '') !== $state) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->label(__('article.fields.slug'))
                                    ->helperText(__('article.fields.slug_help')),
                                Forms\Components\Textarea::make('excerpt')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->label(__('article.fields.excerpt'))
                                    ->helperText(__('article.fields.excerpt_help')),
                            ]),

                        Forms\Components\Section::make(__('article.sections.content'))
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->label(__('article.fields.content'))
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('articles')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ]),

                        Forms\Components\Section::make(__('article.sections.seo'))
                            ->collapsed()
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(500)
                                    ->label(__('article.fields.meta_title'))
                                    ->helperText(__('article.fields.meta_title_help')),
                                Forms\Components\Textarea::make('meta_description')
                                    ->rows(3)
                                    ->label(__('article.fields.meta_description'))
                                    ->helperText(__('article.fields.meta_description_help')),
                                Forms\Components\Textarea::make('meta_keywords')
                                    ->rows(2)
                                    ->label(__('article.fields.meta_keywords')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('article.sections.publishing'))
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => __('article.fields.status_draft'),
                                        'published' => __('article.fields.status_published'),
                                        'scheduled' => __('article.fields.status_scheduled'),
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->label(__('article.fields.status'))
                                    ->live(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label(__('article.fields.published_at'))
                                    ->default(now())
                                    ->visible(fn (Forms\Get $get): bool => in_array($get('status'), ['published', 'scheduled'])),
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('article.fields.category')),
                                Forms\Components\Select::make('author_id')
                                    ->relationship('author', 'name')
                                    ->default(fn () => auth()->id())
                                    ->searchable()
                                    ->preload()
                                    ->label(__('article.fields.author')),
                                Forms\Components\Toggle::make('is_featured')
                                    ->default(false)
                                    ->label(__('article.fields.is_featured')),
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->label(__('article.fields.sort_order')),
                            ]),

                        Forms\Components\Section::make(__('article.sections.media'))
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('articles')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1200')
                                    ->imageResizeTargetHeight('675')
                                    ->label(__('article.fields.featured_image')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->disk('public')
                    ->square()
                    ->label(''),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->label(__('article.fields.title')),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->label(__('article.fields.category')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => __('article.fields.status_draft'),
                        'published' => __('article.fields.status_published'),
                        'scheduled' => __('article.fields.status_scheduled'),
                    })
                    ->label(__('article.fields.status')),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label(__('article.fields.is_featured')),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable()
                    ->label(__('article.fields.view_count')),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('article.fields.published_at')),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('article.fields.author')),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => __('article.fields.status_draft'),
                        'published' => __('article.fields.status_published'),
                        'scheduled' => __('article.fields.status_scheduled'),
                    ])
                    ->label(__('article.filters.status')),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label(__('article.filters.category')),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('article.filters.is_featured')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_on_site')
                    ->label(__('article.actions.view_on_site'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Article $record): string => route('news.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Article $record): bool => $record->status === 'published'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
