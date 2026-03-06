<?php

namespace App\Traits;

use App\Models\SiteSetting;

trait ReplacesContentPlaceholders
{
    public function getRenderedContentAttribute(): ?string
    {
        if ($this->content === null) {
            return null;
        }

        return str_replace(
            '{contact_email}',
            SiteSetting::getValue('schema', 'contact_email', ''),
            $this->content
        );
    }
}
