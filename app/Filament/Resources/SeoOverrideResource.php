<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoOverrideResource\Pages;
use App\Models\SeoOverride;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeoOverrideResource extends Resource
{
    protected static ?string $model = SeoOverride::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.seo');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.seo_overrides.label');
    }

    public static function getModelLabel(): string
    {
        return __('admin.seo_overrides.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.seo_overrides.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.seo_overrides.section_path'))
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->maxLength(255)
                            ->label(__('admin.seo_overrides.fields.label'))
                            ->helperText(__('admin.seo_overrides.fields.label_help')),
                        Forms\Components\TextInput::make('path_pattern')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('/ hoặc /xsmb hoặc /xsmb/*')
                            ->label(__('admin.seo_overrides.fields.path_pattern'))
                            ->helperText(__('admin.seo_overrides.fields.path_pattern_help')),
                        Forms\Components\Select::make('match_type')
                            ->required()
                            ->options([
                                'exact' => __('admin.seo_overrides.fields.match_type_exact'),
                                'wildcard' => __('admin.seo_overrides.fields.match_type_wildcard'),
                            ])
                            ->default('exact')
                            ->label(__('admin.seo_overrides.fields.match_type')),
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->label(__('admin.seo_overrides.fields.priority'))
                            ->helperText(__('admin.seo_overrides.fields.priority_help')),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label(__('admin.seo_overrides.fields.is_active')),
                    ])->columns(2),

                Forms\Components\Section::make(__('admin.seo_overrides.section_meta'))
                    ->schema([
                        Forms\Components\TextInput::make('page_title')
                            ->maxLength(500)
                            ->label(__('admin.seo_overrides.fields.page_title')),
                        Forms\Components\Textarea::make('meta_description')
                            ->rows(3)
                            ->label(__('admin.seo_overrides.fields.meta_description')),
                        Forms\Components\Textarea::make('meta_keywords')
                            ->rows(2)
                            ->label(__('admin.seo_overrides.fields.meta_keywords')),
                        Forms\Components\TextInput::make('robots')
                            ->maxLength(100)
                            ->label(__('admin.seo_overrides.fields.robots'))
                            ->helperText(__('admin.seo_overrides.fields.robots_help')),
                        Forms\Components\TextInput::make('canonical_url')
                            ->maxLength(500)
                            ->url()
                            ->label(__('admin.seo_overrides.fields.canonical_url')),
                    ]),

                Forms\Components\Section::make(__('admin.seo_overrides.section_og'))
                    ->schema([
                        Forms\Components\TextInput::make('og_title')
                            ->maxLength(500)
                            ->label(__('admin.seo_overrides.fields.og_title')),
                        Forms\Components\Textarea::make('og_description')
                            ->rows(3)
                            ->label(__('admin.seo_overrides.fields.og_description')),
                        Forms\Components\TextInput::make('og_image')
                            ->maxLength(500)
                            ->url()
                            ->label(__('admin.seo_overrides.fields.og_image'))
                            ->helperText(__('admin.seo_overrides.fields.og_image_help')),
                    ]),

                Forms\Components\Section::make(__('admin.seo_overrides.section_schema'))
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('schema_jsonld')
                            ->rows(10)
                            ->label(__('admin.seo_overrides.fields.schema_jsonld'))
                            ->helperText(__('admin.seo_overrides.fields.schema_jsonld_help'))
                            ->json(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->label(__('admin.seo_overrides.fields.label')),
                Tables\Columns\TextColumn::make('path_pattern')
                    ->searchable()
                    ->copyable()
                    ->label(__('admin.seo_overrides.fields.path_pattern')),
                Tables\Columns\TextColumn::make('match_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'exact' => 'success',
                        'wildcard' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'exact' => __('admin.seo_overrides.fields.match_type_exact'),
                        'wildcard' => __('admin.seo_overrides.fields.match_type_wildcard'),
                    })
                    ->label(__('admin.seo_overrides.fields.match_type')),
                Tables\Columns\TextColumn::make('page_title')
                    ->limit(40)
                    ->label(__('admin.seo_overrides.fields.page_title')),
                Tables\Columns\TextColumn::make('priority')
                    ->sortable()
                    ->label(__('admin.seo_overrides.fields.priority')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('admin.seo_overrides.fields.is_active')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('priority', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('match_type')
                    ->options([
                        'exact' => __('admin.seo_overrides.fields.match_type_exact'),
                        'wildcard' => __('admin.seo_overrides.fields.match_type_wildcard'),
                    ])
                    ->label(__('admin.seo_overrides.filters.match_type')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('admin.seo_overrides.filters.is_active')),
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
            'index' => Pages\ListSeoOverrides::route('/'),
            'create' => Pages\CreateSeoOverride::route('/create'),
            'edit' => Pages\EditSeoOverride::route('/{record}/edit'),
        ];
    }
}
