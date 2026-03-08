<?php

namespace App\Http\Middleware;

use App\Models\Province;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LegacyRedirects
{
    /**
     * Static redirects: old path => new path
     */
    protected array $staticRedirects = [
        '/xsmb' => '/xsmb-xo-so-mien-bac.html',
        '/xsmt' => '/xsmt-xo-so-mien-trung.html',
        '/xsmn' => '/xsmn-xo-so-mien-nam.html',
        '/xsmb/truc-tiep' => '/xo-so-truc-tiep-mien-bac.html',
        '/xsmt/truc-tiep' => '/xo-so-truc-tiep-mien-trung.html',
        '/xsmn/truc-tiep' => '/xo-so-truc-tiep-mien-nam.html',
        '/du-doan' => '/du-doan.html',
        '/du-doan/xsmb' => '/du-doan-xsmb.html',
        '/du-doan/xsmt' => '/du-doan-xsmt.html',
        '/du-doan/xsmn' => '/du-doan-xsmn.html',
    ];

    /**
     * Region mapping for dynamic redirects
     */
    protected array $regionFullNames = [
        'xsmb' => 'xo-so-mien-bac',
        'xsmt' => 'xo-so-mien-trung',
        'xsmn' => 'xo-so-mien-nam',
    ];

    /**
     * Region slug to DB region mapping
     */
    protected array $regionToDb = [
        'xsmb' => 'north',
        'xsmt' => 'central',
        'xsmn' => 'south',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->path(), '/');

        // Check static redirects
        if (isset($this->staticRedirects[$path])) {
            return redirect($this->staticRedirects[$path], 301);
        }

        // Check dynamic patterns

        // Regional date: /xsmb/01-01-2024 → /xsmb-xo-so-mien-bac-01-01-2024.html
        if (preg_match('#^/(xsmb|xsmt|xsmn)/(\d{2}-\d{2}-\d{4})$#', $path, $m)) {
            $region = $m[1];
            $date = $m[2];
            $fullName = $this->regionFullNames[$region];
            return redirect("/{$region}-{$fullName}-{$date}.html", 301);
        }

        // Day of week: /xsmb/thu-2 → /xsmb-thu-2.html
        if (preg_match('#^/(xsmb|xsmt|xsmn)/(thu-[2-7]|chu-nhat)$#', $path, $m)) {
            return redirect("/{$m[1]}-{$m[2]}.html", 301);
        }

        // Province: /xsmn/an-giang → /xs{code}-sx{code}-xo-so-an-giang.html
        if (preg_match('#^/(xsmb|xsmt|xsmn)/([a-z0-9]+(?:-[a-z0-9]+)*)$#', $path, $m)) {
            $region = $m[1];
            $slug = $m[2];
            $dbRegion = $this->regionToDb[$region] ?? null;

            if ($dbRegion) {
                $province = $this->getProvinceBySlug($slug);
                if ($province && $province->region === $dbRegion) {
                    $code = $province->code;
                    return redirect("/xs{$code}-sx{$code}-xo-so-{$slug}.html", 301);
                }
            }
        }

        // Prediction detail: /du-doan/du-doan-xsmb-... → /du-doan-xsmb-...
        if (preg_match('#^/du-doan/(du-doan-(?:xsmb|xsmt|xsmn)-.+\.html)$#', $path, $m)) {
            return redirect("/{$m[1]}", 301);
        }

        return $next($request);
    }

    protected function getProvinceBySlug(string $slug): ?Province
    {
        return Cache::remember("province_slug_{$slug}", 3600, function () use ($slug) {
            return Province::where('slug', $slug)->where('is_active', true)->first();
        });
    }
}
