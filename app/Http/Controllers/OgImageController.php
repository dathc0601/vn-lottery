<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class OgImageController extends Controller
{
    private const WIDTH = 1200;
    private const HEIGHT = 630;

    private const COLORS = [
        'xsmb' => ['bg' => [195, 57, 43], 'accent' => [231, 76, 60]],
        'xsmt' => ['bg' => [212, 160, 23], 'accent' => [241, 196, 15]],
        'xsmn' => ['bg' => [39, 174, 96], 'accent' => [46, 204, 113]],
    ];

    private const REGION_NAMES = [
        'xsmb' => 'MIỀN BẮC',
        'xsmt' => 'MIỀN TRUNG',
        'xsmn' => 'MIỀN NAM',
    ];

    public function prediction(string $regionSlug, string $date)
    {
        if (!in_array($regionSlug, ['xsmb', 'xsmt', 'xsmn'])) {
            abort(404);
        }

        if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            abort(404);
        }

        $cacheKey = "og_image:prediction:{$regionSlug}:{$date}";

        $png = Cache::remember($cacheKey, now()->addDays(30), function () use ($regionSlug, $date) {
            return base64_encode($this->generatePredictionImage($regionSlug, $date));
        });

        return response(base64_decode($png), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    private function generatePredictionImage(string $regionSlug, string $date): string
    {
        $img = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        $colors = self::COLORS[$regionSlug];
        $bg = imagecolorallocate($img, ...$colors['bg']);
        $accent = imagecolorallocate($img, ...$colors['accent']);
        $white = imagecolorallocate($img, 255, 255, 255);
        $whiteTranslucent = imagecolorallocatealpha($img, 255, 255, 255, 80);
        $darkOverlay = imagecolorallocatealpha($img, 0, 0, 0, 80);

        imagefill($img, 0, 0, $bg);

        // Accent stripe at top
        imagefilledrectangle($img, 0, 0, self::WIDTH, 8, $accent);

        // Accent stripe at bottom
        imagefilledrectangle($img, 0, self::HEIGHT - 8, self::WIDTH, self::HEIGHT, $accent);

        $fontBold = $this->findFont('bold');
        $fontRegular = $this->findFont('regular');

        $regionName = self::REGION_NAMES[$regionSlug] ?? strtoupper($regionSlug);
        $regionCode = strtoupper($regionSlug);

        // Format date for display: dd-mm-yyyy → dd/mm/yyyy
        $displayDate = str_replace('-', '/', $date);

        // Line 1: "DỰ ĐOÁN KẾT QUẢ"
        $this->drawCenteredText($img, 'DỰ ĐOÁN KẾT QUẢ', $fontBold, 36, $whiteTranslucent, 160);

        // Line 2: "XỔ SỐ MIỀN BẮC" (large)
        $this->drawCenteredText($img, 'XỔ SỐ ' . $regionName, $fontBold, 52, $white, 240);

        // Date pill background
        $dateText = $displayDate;
        $dateFontSize = 48;
        $dateBBox = imagettfbbox($dateFontSize, 0, $fontBold, $dateText);
        $dateWidth = $dateBBox[2] - $dateBBox[0];
        $pillPadX = 50;
        $pillPadY = 16;
        $pillX1 = (self::WIDTH - $dateWidth) / 2 - $pillPadX;
        $pillX2 = (self::WIDTH + $dateWidth) / 2 + $pillPadX;
        $pillY1 = 310;
        $pillY2 = 310 + $dateFontSize + $pillPadY * 2;

        // Draw rounded rectangle for date pill
        $this->drawRoundedRect($img, (int)$pillX1, $pillY1, (int)$pillX2, (int)$pillY2, 12, $darkOverlay);

        // Date text
        $this->drawCenteredText($img, $dateText, $fontBold, $dateFontSize, $white, $pillY1 + $pillPadY + $dateFontSize - 4);

        // Line 4: Region code (smaller, below date)
        $this->drawCenteredText($img, 'SOI CẦU ' . $regionCode, $fontRegular, 28, $whiteTranslucent, 440);

        // Bottom: site name
        $this->drawCenteredText($img, config('app.name', 'XSKT.VN'), $fontRegular, 22, $whiteTranslucent, self::HEIGHT - 40);

        ob_start();
        imagepng($img, null, 6);
        $png = ob_get_clean();

        imagedestroy($img);

        return $png;
    }

    private function drawCenteredText($img, string $text, string $font, int $size, $color, int $y): void
    {
        $bbox = imagettfbbox($size, 0, $font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = (self::WIDTH - $textWidth) / 2;
        imagettftext($img, $size, 0, (int)$x, $y, $color, $font, $text);
    }

    private function drawRoundedRect($img, int $x1, int $y1, int $x2, int $y2, int $radius, $color): void
    {
        imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    private function findFont(string $weight): string
    {
        $isBold = $weight === 'bold';

        $candidates = $isBold
            ? [
                storage_path('fonts/Roboto-Bold.ttf'),
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            ]
            : [
                storage_path('fonts/Roboto-Regular.ttf'),
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Last resort: return first candidate (will error if missing, but better than silent failure)
        return $candidates[0];
    }
}
