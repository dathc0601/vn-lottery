<?php

namespace App\Filament\Resources\NumberStatisticResource\Pages;

use App\Filament\Resources\NumberStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNumberStatistics extends ListRecords
{
    protected static string $resource = NumberStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
