<?php

namespace App\Services;

use App\Models\SeoOverride;

class SeoOverrideService
{
    protected ?SeoOverride $override = null;
    protected bool $resolved = false;

    /**
     * Resolve the override for the current request path. Called once by middleware.
     */
    public function resolveForPath(string $path): void
    {
        if (!$this->resolved) {
            $this->override = SeoOverride::findForPath($path);
            $this->resolved = true;
        }
    }

    /**
     * Get the matched override, or null.
     */
    public function getOverride(): ?SeoOverride
    {
        return $this->override;
    }

    /**
     * Check if the override has a non-null, non-empty value for the given field.
     */
    public function has(string $field): bool
    {
        if (!$this->override) {
            return false;
        }

        $value = $this->override->getAttribute($field);

        if (is_null($value)) {
            return false;
        }

        if (is_string($value)) {
            return $value !== '';
        }

        // For arrays (schema_jsonld), check if non-empty
        if (is_array($value)) {
            return !empty($value);
        }

        return true;
    }

    /**
     * Get a field value from the override, with optional default.
     */
    public function get(string $field, ?string $default = null): ?string
    {
        if (!$this->has($field)) {
            return $default;
        }

        $value = $this->override->getAttribute($field);

        // For schema_jsonld (array), return JSON-encoded string
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return $value;
    }

    /**
     * Clear the overrides cache.
     */
    public function clearCache(): void
    {
        SeoOverride::clearCache();
    }
}
