<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NumberStatisticResource\Pages;
use App\Filament\Resources\NumberStatisticResource\RelationManagers;
use App\Models\NumberStatistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NumberStatisticResource extends Resource
{
    protected static ?string $model = NumberStatistic::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.lottery_data');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('province_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(2),
                Forms\Components\TextInput::make('frequency_30d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('frequency_60d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('frequency_90d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('frequency_100d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('frequency_200d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('frequency_300d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('frequency_500d')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DatePicker::make('last_appeared'),
                Forms\Components\TextInput::make('cycle_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Province')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency_30d')
                    ->label('30d')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency_60d')
                    ->label('60d')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency_90d')
                    ->label('90d')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency_100d')
                    ->label('100d')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('frequency_200d')
                    ->label('200d')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('frequency_300d')
                    ->label('300d')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('frequency_500d')
                    ->label('500d')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_appeared')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cycle_count')
                    ->label('Cycle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Province')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('frequency_30d', 'desc');
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
            'index' => Pages\ListNumberStatistics::route('/'),
            'analytics' => Pages\AnalyzeNumberStatistics::route('/analytics'),
            'create' => Pages\CreateNumberStatistic::route('/create'),
            'edit' => Pages\EditNumberStatistic::route('/{record}/edit'),
        ];
    }
}
