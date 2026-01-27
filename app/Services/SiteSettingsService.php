<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class SiteSettingsService
{
    /**
     * Get a single setting value
     */
    public function get(string $group, string $key, ?string $default = null): ?string
    {
        return SiteSetting::getValue($group, $key, $default);
    }

    /**
     * Get all settings for a group
     */
    public function group(string $group): array
    {
        return SiteSetting::getGroup($group);
    }

    /**
     * Get the public URL for an uploaded image setting
     */
    public function imageUrl(string $group, string $key): ?string
    {
        $path = $this->get($group, $key);

        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Build the page title from the template
     */
    public function buildTitle(?string $pageTitle = null): string
    {
        $siteName = $this->get('general', 'site_name', 'XSKT.VN');
        $template = $this->get('meta', 'title_template', '{page_title} | {site_name}');

        if (!$pageTitle) {
            return $siteName;
        }

        return str_replace(
            ['{page_title}', '{site_name}'],
            [$pageTitle, $siteName],
            $template
        );
    }

    /**
     * Build the robots meta content
     */
    public function robotsMeta(): string
    {
        $index = $this->get('meta', 'robots_index', '1') ? 'index' : 'noindex';
        $follow = $this->get('meta', 'robots_follow', '1') ? 'follow' : 'nofollow';

        return "{$index}, {$follow}";
    }

    /**
     * Build the copyright text
     */
    public function copyrightText(): string
    {
        $template = $this->get('footer', 'copyright_template', '© {year} {site_name}');
        $siteName = $this->get('general', 'site_name', 'XSKT.VN');

        return str_replace(
            ['{year}', '{site_name}'],
            [date('Y'), $siteName],
            $template
        );
    }

    /**
     * Build the Organization JSON-LD schema
     */
    public function organizationSchema(): ?string
    {
        $orgName = $this->get('schema', 'org_name');

        if (!$orgName) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $orgName,
        ];

        $orgUrl = $this->get('schema', 'org_url');
        if ($orgUrl) {
            $schema['url'] = $orgUrl;
        }

        $orgLogo = $this->imageUrl('schema', 'org_logo');
        if ($orgLogo) {
            $schema['logo'] = $orgLogo;
        }

        $email = $this->get('schema', 'contact_email');
        $phone = $this->get('schema', 'contact_phone');

        if ($email || $phone) {
            $contactPoint = [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
            ];

            if ($email) {
                $contactPoint['email'] = $email;
            }

            if ($phone) {
                $contactPoint['telephone'] = $phone;
            }

            $schema['contactPoint'] = $contactPoint;
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Clear all settings cache
     */
    public function clearCache(): void
    {
        SiteSetting::clearCache();
    }
}
