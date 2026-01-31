<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResource\Pages;
use App\Models\Prediction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PredictionResource extends Resource
{
    protected static ?string $model = Prediction::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return 'Dự đoán';
    }

    public static function getModelLabel(): string
    {
        return 'Dự đoán';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Dự đoán';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin cơ bản')
                            ->schema([
                                Forms\Components\Select::make('region')
                                    ->options(Prediction::REGIONS)
                                    ->required()
                                    ->label('Khu vực')
                                    ->disabled(fn ($record) => $record !== null),
                                Forms\Components\DatePicker::make('prediction_date')
                                    ->required()
                                    ->label('Ngày dự đoán')
                                    ->disabled(fn ($record) => $record !== null),
                                Forms\Components\DatePicker::make('reference_date')
                                    ->required()
                                    ->label('Ngày tham chiếu'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Chờ xử lý',
                                        'generated' => 'Đã tạo',
                                        'published' => 'Đã xuất bản',
                                    ])
                                    ->required()
                                    ->label('Trạng thái'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Dự đoán chính')
                            ->schema([
                                Forms\Components\KeyValue::make('predictions_data.head_tail.combined')
                                    ->label('Đầu đuôi giải ĐB')
                                    ->keyLabel('STT')
                                    ->valueLabel('Số')
                                    ->reorderable(),
                                Forms\Components\TagsInput::make('predictions_data.loto_2_digit')
                                    ->label('Loto 2 số hay về')
                                    ->placeholder('Thêm số...'),
                                Forms\Components\TagsInput::make('predictions_data.loto_3_digit')
                                    ->label('Lô tô 3 số')
                                    ->placeholder('Thêm số...'),
                                Forms\Components\TagsInput::make('predictions_data.vip_4_digit')
                                    ->label('Soi cầu 4 số VIP')
                                    ->placeholder('Thêm số...'),
                            ]),

                        Forms\Components\Section::make('Phân tích số đẹp')
                            ->collapsed()
                            ->schema([
                                Forms\Components\TagsInput::make('analysis_data.bach_thu')
                                    ->label('Bạch thủ'),
                                Forms\Components\TagsInput::make('analysis_data.lat_lien_tuc')
                                    ->label('Lật liên tục'),
                                Forms\Components\TagsInput::make('analysis_data.cau_2_nhay')
                                    ->label('Cầu 2 nháy'),
                                Forms\Components\TagsInput::make('analysis_data.pascal_triangle')
                                    ->label('Tam giác Pascal'),
                                Forms\Components\TagsInput::make('analysis_data.lo_kep')
                                    ->label('Cầu lô kẹp'),
                                Forms\Components\TagsInput::make('analysis_data.loto_hay_ve')
                                    ->label('Lô tô hay về'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin bài viết')
                            ->schema([
                                Forms\Components\Select::make('article_id')
                                    ->relationship('article', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->label('Bài viết liên kết'),
                                Forms\Components\DateTimePicker::make('generated_at')
                                    ->label('Thời gian tạo')
                                    ->disabled(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Thời gian xuất bản'),
                            ]),

                        Forms\Components\Section::make('Thống kê')
                            ->collapsed()
                            ->schema([
                                Forms\Components\Placeholder::make('statistics_info')
                                    ->label('')
                                    ->content(fn ($record) => $record
                                        ? 'Xem thống kê chi tiết trong JSON bên dưới'
                                        : 'Chưa có dữ liệu'),
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
                Tables\Columns\TextColumn::make('region')
                    ->formatStateUsing(fn (string $state): string => Prediction::REGIONS[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'north' => 'danger',
                        'central' => 'warning',
                        'south' => 'success',
                    })
                    ->sortable()
                    ->label('Khu vực'),
                Tables\Columns\TextColumn::make('prediction_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Ngày dự đoán'),
                Tables\Columns\TextColumn::make('reference_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Ngày tham chiếu'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'generated' => 'warning',
                        'published' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'generated' => 'Đã tạo',
                        'published' => 'Đã xuất bản',
                    })
                    ->sortable()
                    ->label('Trạng thái'),
                Tables\Columns\TextColumn::make('article.title')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Bài viết'),
                Tables\Columns\TextColumn::make('generated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Thời gian tạo'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Thời gian xuất bản'),
            ])
            ->defaultSort('prediction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->options(Prediction::REGIONS)
                    ->label('Khu vực'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'generated' => 'Đã tạo',
                        'published' => 'Đã xuất bản',
                    ])
                    ->label('Trạng thái'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_on_site')
                    ->label('Xem trên site')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Prediction $record): string => $record->url)
                    ->openUrlInNewTab()
                    ->visible(fn (Prediction $record): bool => $record->status === 'published'),
                Tables\Actions\Action::make('publish')
                    ->label('Xuất bản')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Prediction $record) {
                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]);
                    })
                    ->visible(fn (Prediction $record): bool => $record->status !== 'published'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Xuất bản')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'published',
                                    'published_at' => now(),
                                ]);
                            });
                        }),
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
            'index' => Pages\ListPredictions::route('/'),
            'create' => Pages\CreatePrediction::route('/create'),
            'edit' => Pages\EditPrediction::route('/{record}/edit'),
        ];
    }
}
