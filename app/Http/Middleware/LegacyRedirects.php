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

    /**
     * Old province codes mapped to their slug (for redirecting old URLs)
     */
    protected array $oldCodeToSlug = [
        // Miền Bắc
        'miba' => 'ha-noi',
        'quni' => 'quang-ninh',
        'bani' => 'bac-ninh',
        'haph' => 'hai-phong',
        'nadi' => 'nam-dinh',
        'thbi' => 'thai-binh',
        // Miền Trung
        'bidi' => 'binh-dinh',
        'dana' => 'da-nang',
        'dalak' => 'dak-lak',
        'dano' => 'dak-nong',
        'gila' => 'gia-lai',
        'khho' => 'khanh-hoa',
        'kotu' => 'kon-tum',
        'nith' => 'ninh-thuan',
        'phye' => 'phu-yen',
        'qubi' => 'quang-binh',
        'quna' => 'quang-nam',
        'qung' => 'quang-ngai',
        'qutr' => 'quang-tri',
        'thth' => 'thua-thien-hue',
        // Miền Nam
        'angi' => 'an-giang',
        'bali' => 'bac-lieu',
        'betre' => 'ben-tre',
        'bidu' => 'binh-duong',
        'biph' => 'binh-phuoc',
        'cama' => 'ca-mau',
        'cath' => 'can-tho',
        'dona' => 'dong-nai',
        'doth' => 'dong-thap',
        'hagi' => 'hau-giang',
        'kigi' => 'kien-giang',
        'loan' => 'long-an',
        'sotr' => 'soc-trang',
        'tani' => 'tay-ninh',
        'tigi' => 'tien-giang',
        'trvi' => 'tra-vinh',
        'vilo' => 'vinh-long',
        'vuta' => 'vung-tau',
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

        // Province: /xsmn/an-giang → /xs{code}-xo-so-an-giang.html
        if (preg_match('#^/(xsmb|xsmt|xsmn)/([a-z0-9]+(?:-[a-z0-9]+)*)$#', $path, $m)) {
            $region = $m[1];
            $slug = $m[2];
            $dbRegion = $this->regionToDb[$region] ?? null;

            if ($dbRegion) {
                $province = $this->getProvinceBySlug($slug);
                if ($province && $province->region === $dbRegion) {
                    $code = $province->code;
                    return redirect("/xs{$code}-xo-so-{$slug}.html", 301);
                }
            }
        }

        // Old province URL format: /xs{oldCode}-sx{oldCode}-xo-so-{slug}.html → /xs{newCode}-xo-so-{slug}.html
        if (preg_match('#^/xs([a-z0-9]+)-sx[a-z0-9]+-xo-so-([a-z0-9\-]+)\.html$#', $path, $m)) {
            $oldCode = $m[1];
            $slug = $m[2];

            // Check if this is an old code that needs redirecting
            if (isset($this->oldCodeToSlug[$oldCode])) {
                $province = $this->getProvinceBySlug($slug);
                if ($province) {
                    $newCode = $province->code;
                    return redirect("/xs{$newCode}-xo-so-{$slug}.html", 301);
                }
            }

            // Also handle current codes in old URL format (xs{code}-sx{code}-xo-so-...)
            $province = $this->getProvinceBySlug($slug);
            if ($province && $province->code === $oldCode) {
                return redirect("/xs{$oldCode}-xo-so-{$slug}.html", 301);
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
