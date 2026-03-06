<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Forms\Components\TinyEditor;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $slug = 'custom-pages';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('page.pages.label');
    }

    public static function getModelLabel(): string
    {
        return __('page.pages.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('page.pages.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('page.sections.basic'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(500)
                                    ->label(__('page.fields.title'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Forms\Set $set, ?string $old, $livewire) {
                                        if ($livewire instanceof Pages\CreatePage && ($old ?? '') !== $state) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->label(__('page.fields.slug'))
                                    ->helperText(__('page.fields.slug_help')),
                            ]),

                        Forms\Components\Section::make(__('page.sections.content'))
                            ->schema([
                                TinyEditor::make('content')
                                    ->required()
                                    ->label(__('page.fields.content')),
                            ]),

                        Forms\Components\Section::make(__('page.sections.seo'))
                            ->collapsed()
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(500)
                                    ->label(__('page.fields.meta_title'))
                                    ->helperText(__('page.fields.meta_title_help')),
                                Forms\Components\Textarea::make('meta_description')
                                    ->rows(3)
                                    ->label(__('page.fields.meta_description'))
                                    ->helperText(__('page.fields.meta_description_help')),
                                Forms\Components\Textarea::make('meta_keywords')
                                    ->rows(2)
                                    ->label(__('page.fields.meta_keywords')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('page.sections.publishing'))
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => __('page.fields.status_draft'),
                                        'published' => __('page.fields.status_published'),
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->label(__('page.fields.status'))
                                    ->live()
                                    ->afterStateUpdated(function (string $state, Forms\Set $set, Forms\Get $get) {
                                        if ($state === 'published' && !$get('published_at')) {
                                            $set('published_at', now());
                                        }
                                    }),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label(__('page.fields.published_at'))
                                    ->default(now())
                                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'published'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->label(__('page.fields.sort_order')),
                            ]),

                        Forms\Components\Section::make(__('page.sections.media'))
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('pages')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1200')
                                    ->imageResizeTargetHeight('675')
                                    ->label(__('page.fields.featured_image')),
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
                    ->label(__('page.fields.title')),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->label(__('page.fields.slug')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => __('page.fields.status_draft'),
                        'published' => __('page.fields.status_published'),
                    })
                    ->label(__('page.fields.status')),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable()
                    ->label(__('page.fields.view_count')),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('page.fields.published_at')),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => __('page.fields.status_draft'),
                        'published' => __('page.fields.status_published'),
                    ])
                    ->label(__('page.filters.status')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_on_site')
                    ->label(__('page.actions.view_on_site'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Page $record): string => route('page.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Page $record): bool => $record->status === 'published'),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
