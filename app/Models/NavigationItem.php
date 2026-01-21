<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class NavigationItem extends Model
{
    protected $fillable = [
        'title',
        'title_short',
        'parent_id',
        'type',
        'route_name',
        'route_params',
        'url',
        'active_pattern',
        'dropdown_type',
        'icon',
        'sort_order',
        'is_active',
        'open_in_new_tab',
    ];

    protected $casts = [
        'route_params' => 'array',
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public const CACHE_KEY = 'navigation_items_tree';
    public const CACHE_TTL = 3600; // 1 hour

    /**
     * Parent navigation item relationship
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    /**
     * Child navigation items relationship
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Get all children including inactive ones (for admin)
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')
            ->orderBy('sort_order');
    }

    /**
     * Get cached navigation tree
     */
    public static function getCachedTree(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children'])
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Clear the navigation cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Get the URL for this navigation item
     */
    public function getUrl(): ?string
    {
        if ($this->type === 'static_link' && $this->url) {
            return $this->url;
        }

        if ($this->type === 'route' && $this->route_name) {
            try {
                return route($this->route_name, $this->route_params ?? []);
            } catch (\Exception $e) {
                return null;
            }
        }

        // For special types (xsmb_days, xsmt_days, xsmn_days), return the main route
        if ($this->isSpecialType()) {
            return match ($this->type) {
                'xsmb_days' => route('xsmb'),
                'xsmt_days' => route('xsmt'),
                'xsmn_days' => route('xsmn'),
                default => null,
            };
        }

        return null;
    }

    /**
     * Check if current request matches this navigation item
     */
    public function isActive(): bool
    {
        if ($this->active_pattern) {
            return request()->routeIs($this->active_pattern);
        }

        if ($this->route_name) {
            return request()->routeIs($this->route_name);
        }

        return false;
    }

    /**
     * Check if this item is a special type (requires custom rendering)
     */
    public function isSpecialType(): bool
    {
        return in_array($this->type, ['xsmb_days', 'xsmt_days', 'xsmn_days']);
    }

    /**
     * Check if this item has a dropdown
     */
    public function hasDropdown(): bool
    {
        // Special types have dynamic dropdowns, route/static_link have dropdowns when they have children
        return $this->isSpecialType() || $this->children->count() > 0;
    }

    /**
     * Get dropdown type for special items
     */
    public function getEffectiveDropdownType(): string
    {
        // All special types use simple dropdown (days of the week)
        if ($this->isSpecialType()) {
            return 'simple';
        }

        return $this->dropdown_type;
    }

    /**
     * Get type options for forms
     */
    public static function getTypeOptions(): array
    {
        return [
            'route' => 'Route (Laravel)',
            'static_link' => 'Link tĩnh',
            'xsmb_days' => 'XSMB theo ngày',
            'xsmt_days' => 'XSMT theo ngày',
            'xsmn_days' => 'XSMN theo ngày',
            'divider' => 'Phân cách',
        ];
    }

    /**
     * Get dropdown type options for forms
     */
    public static function getDropdownTypeOptions(): array
    {
        return [
            'simple' => 'Dropdown đơn giản',
            'mega_menu' => 'Mega menu',
        ];
    }
}
