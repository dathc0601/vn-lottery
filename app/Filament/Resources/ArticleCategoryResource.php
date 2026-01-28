<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('article.categories.label');
    }

    public static function getModelLabel(): string
    {
        return __('article.categories.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('article.categories.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('article.sections.basic'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('article.fields.name'))
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
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->label(__('article.fields.description')),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label(__('article.fields.sort_order')),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label(__('article.fields.is_active')),
                    ])->columns(2),

                Forms\Components\Section::make(__('article.sections.seo'))
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->maxLength(500)
                            ->label(__('article.fields.meta_title')),
                        Forms\Components\Textarea::make('meta_description')
                            ->rows(3)
                            ->label(__('article.fields.meta_description')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('article.fields.name')),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->label(__('article.fields.slug')),
                Tables\Columns\TextColumn::make('articles_count')
                    ->counts('articles')
                    ->sortable()
                    ->label(__('article.articles.label')),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label(__('article.fields.sort_order')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('article.fields.is_active')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('article.fields.is_active')),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }
}
