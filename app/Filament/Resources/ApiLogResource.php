<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiLogResource\Pages;
use App\Filament\Resources\ApiLogResource\RelationManagers;
use App\Models\ApiLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiLogResource extends Resource
{
    protected static ?string $model = ApiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.system');
    }

    public static function getNavigationLabel(): string
    {
        return 'Nhật ký API';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('endpoint')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('province_code')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('response_status')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('response_time_ms')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('error_message')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('fetched_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('response_status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('response_time_ms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fetched_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListApiLogs::route('/'),
            'create' => Pages\CreateApiLog::route('/create'),
            'edit' => Pages\EditApiLog::route('/{record}/edit'),
        ];
    }
}
