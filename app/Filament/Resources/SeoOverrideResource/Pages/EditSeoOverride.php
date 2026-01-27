<?php

namespace App\Filament\Resources\SeoOverrideResource\Pages;

use App\Filament\Resources\SeoOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeoOverride extends EditRecord
{
    protected static string $resource = SeoOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
