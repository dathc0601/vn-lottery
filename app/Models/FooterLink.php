<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterLink extends Model
{
    protected $fillable = [
        'footer_column_id',
        'label',
        'type',
        'route_name',
        'url',
        'open_in_new_tab',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            FooterColumn::clearCache();
        });

        static::deleted(function () {
            FooterColumn::clearCache();
        });
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(FooterColumn::class, 'footer_column_id');
    }

    public function getUrl(): ?string
    {
        if ($this->type === 'url' && $this->url) {
            return $this->url;
        }

        if ($this->type === 'route' && $this->route_name) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
