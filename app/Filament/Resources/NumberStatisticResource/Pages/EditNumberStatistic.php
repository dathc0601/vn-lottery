<?php

namespace App\Filament\Resources\NumberStatisticResource\Pages;

use App\Filament\Resources\NumberStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNumberStatistic extends EditRecord
{
    protected static string $resource = NumberStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
