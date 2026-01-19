<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.lottery_data');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('province.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('province.fields.name')),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->label(__('province.fields.code'))
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->label(__('province.fields.slug'))
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('region')
                            ->required()
                            ->label(__('province.fields.region'))
                            ->options([
                                'north' => __('admin.regions.north'),
                                'central' => __('admin.regions.central'),
                                'south' => __('admin.regions.south'),
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make(__('province.sections.draw_schedule'))
                    ->schema([
                        Forms\Components\TimePicker::make('draw_time')
                            ->required()
                            ->label(__('province.fields.draw_time')),
                        Forms\Components\CheckboxList::make('draw_days')
                            ->options([
                                '1' => __('admin.days.monday'),
                                '2' => __('admin.days.tuesday'),
                                '3' => __('admin.days.wednesday'),
                                '4' => __('admin.days.thursday'),
                                '5' => __('admin.days.friday'),
                                '6' => __('admin.days.saturday'),
                                '7' => __('admin.days.sunday'),
                            ])
                            ->columns(3)
                            ->label(__('province.fields.draw_days')),
                    ])->columns(1),

                Forms\Components\Section::make(__('province.sections.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label(__('province.fields.sort_order')),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label(__('province.fields.is_active')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('draw_time'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->label(__('province.filters.region'))
                    ->options([
                        'north' => __('admin.regions.north'),
                        'central' => __('admin.regions.central'),
                        'south' => __('admin.regions.south'),
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('province.filters.active_status')),
            ])
            ->actions([
                Tables\Actions\Action::make('fetchNow')
                    ->label(__('province.actions.fetch_now'))
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (Province $record) {
                        \App\Jobs\FetchLotteryResultsJob::dispatch($record->code);
                        \Filament\Notifications\Notification::make()
                            ->title(__('province.actions.fetch_dispatched'))
                            ->body(__('province.actions.fetching_for', ['name' => $record->name]))
                            ->success()
                            ->send();
                    }),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
}
