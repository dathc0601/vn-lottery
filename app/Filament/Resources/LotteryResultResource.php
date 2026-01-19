<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LotteryResultResource\Pages;
use App\Filament\Resources\LotteryResultResource\RelationManagers;
use App\Models\LotteryResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LotteryResultResource extends Resource
{
    protected static ?string $model = LotteryResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.lottery_data');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('lottery_result.sections.draw_info'))
                    ->schema([
                        Forms\Components\Select::make('province_id')
                            ->relationship('province', 'name')
                            ->required()
                            ->searchable()
                            ->label(__('lottery_result.fields.province')),
                        Forms\Components\TextInput::make('turn_num')
                            ->required()
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.turn_num')),
                        Forms\Components\DatePicker::make('draw_date')
                            ->required()
                            ->label(__('lottery_result.fields.draw_date')),
                        Forms\Components\DateTimePicker::make('draw_time')
                            ->required()
                            ->label(__('lottery_result.fields.draw_time')),
                    ])->columns(2),

                Forms\Components\Section::make(__('lottery_result.sections.prize_results'))
                    ->schema([
                        Forms\Components\TextInput::make('prize_special')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_special')),
                        Forms\Components\TextInput::make('prize_1')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_1')),
                        Forms\Components\TextInput::make('prize_2')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_2')),
                        Forms\Components\TextInput::make('prize_3')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_3')),
                        Forms\Components\Textarea::make('prize_4')
                            ->label(__('lottery_result.fields.prize_4')),
                        Forms\Components\TextInput::make('prize_5')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_5')),
                        Forms\Components\TextInput::make('prize_6')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_6')),
                        Forms\Components\TextInput::make('prize_7')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_7')),
                        Forms\Components\TextInput::make('prize_8')
                            ->maxLength(255)
                            ->label(__('lottery_result.fields.prize_8')),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province.name')
                    ->label(__('lottery_result.fields.province'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('turn_num')
                    ->label(__('lottery_result.fields.turn_num'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('draw_date')
                    ->label(__('lottery_result.fields.draw_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('draw_time')
                    ->label(__('lottery_result.fields.draw_time'))
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_special')
                    ->label(__('lottery_result.fields.prize_special'))
                    ->searchable()
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('prize_1')
                    ->label(__('lottery_result.fields.prize_1'))
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('lottery_result.fields.status'))
                    ->formatStateUsing(fn ($state) => $state == 2 ? __('lottery_result.status.completed') : __('lottery_result.status.pending'))
                    ->color(fn ($state) => $state == 2 ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->label(__('Vùng miền'))
                    ->options([
                        'north' => 'Miền Bắc',
                        'central' => 'Miền Trung',
                        'south' => 'Miền Nam',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value'] !== '') {
                            $query->whereHas('province', function($q) use ($data) {
                                $q->where('region', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\SelectFilter::make('province_id')
                    ->relationship('province', 'name')
                    ->label(__('lottery_result.filters.province'))
                    ->searchable(),
                Tables\Filters\Filter::make('draw_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('lottery_result.filters.from_date')),
                        Forms\Components\DatePicker::make('to')
                            ->label(__('lottery_result.filters.to_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date) => $query->whereDate('draw_date', '>=', $date))
                            ->when($data['to'], fn (Builder $query, $date) => $query->whereDate('draw_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('draw_date', 'desc')
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
            'index' => Pages\ListLotteryResults::route('/'),
            'create' => Pages\CreateLotteryResult::route('/create'),
            'edit' => Pages\EditLotteryResult::route('/{record}/edit'),
        ];
    }
}
