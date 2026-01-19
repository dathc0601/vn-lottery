<?php

namespace App\Filament\Resources\LotteryResultResource\Pages;

use App\Filament\Resources\LotteryResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLotteryResult extends EditRecord
{
    protected static string $resource = LotteryResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
