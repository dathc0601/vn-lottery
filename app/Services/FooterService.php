<?php

namespace App\Services;

use App\Models\FooterColumn;
use Illuminate\Support\Collection;

class FooterService
{
    public function getColumns(): Collection
    {
        return FooterColumn::getCachedColumns();
    }

    public function clearCache(): void
    {
        FooterColumn::clearCache();
    }
}
