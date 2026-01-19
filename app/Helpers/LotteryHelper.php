<?php

namespace App\Helpers;

use Carbon\Carbon;

class LotteryHelper
{
    /**
     * Format a comma-separated prize string into an array
     *
     * @param string|null $prizeString
     * @return array
     */
    public static function formatPrizeList(?string $prizeString): array
    {
        if (empty($prizeString)) {
            return [];
        }

        return array_filter(explode(',', $prizeString));
    }

    /**
     * Get Vietnamese label for region
     *
     * @param string $region
     * @return string
     */
    public static function getRegionLabel(string $region): string
    {
        return match($region) {
            'north' => 'Miền Bắc',
            'central' => 'Miền Trung',
            'south' => 'Miền Nam',
            default => $region,
        };
    }

    /**
     * Get Vietnamese day of week name
     *
     * @param Carbon $date
     * @return string
     */
    public static function getVietnameseDayOfWeek(Carbon $date): string
    {
        $days = [
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
        ];

        return $days[$date->dayOfWeek];
    }

    /**
     * Get region badge color class
     *
     * @param string $region
     * @return string
     */
    public static function getRegionBadgeColor(string $region): string
    {
        return match($region) {
            'north' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'central' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            'south' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /**
     * Format draw schedule into human-readable Vietnamese text
     *
     * @param array $drawDays Array of day numbers (1=Monday, 7=Sunday)
     * @param string $drawTime Time in HH:MM:SS format
     * @return string
     */
    public static function formatDrawSchedule(array $drawDays, string $drawTime): string
    {
        $dayNames = [
            1 => 'T2',  // Thứ Hai
            2 => 'T3',  // Thứ Ba
            3 => 'T4',  // Thứ Tư
            4 => 'T5',  // Thứ Năm
            5 => 'T6',  // Thứ Sáu
            6 => 'T7',  // Thứ Bảy
            7 => 'CN',  // Chủ Nhật
        ];

        // Format time to HH:MM
        $time = Carbon::parse($drawTime)->format('H:i');

        // Sort days for consistent comparison
        sort($drawDays);

        // Check if all days (daily)
        if (count($drawDays) === 7) {
            return "Hằng ngày lúc {$time}";
        }

        // Check for weekdays only (Monday-Friday)
        if ($drawDays === [1, 2, 3, 4, 5]) {
            return "T2-T6 lúc {$time}";
        }

        // Check for weekends only (Saturday-Sunday)
        if ($drawDays === [6, 7]) {
            return "T7, CN lúc {$time}";
        }

        // List specific days
        $days = array_map(fn($day) => $dayNames[$day] ?? $day, $drawDays);
        return implode(', ', $days) . " lúc {$time}";
    }

    /**
     * Get all two-digit numbers from a lottery result (all prizes)
     *
     * @param \App\Models\LotteryResult $result
     * @return array
     */
    public static function getAllNumbers($result): array
    {
        $numbers = [];

        // Prize mapping based on XSMB structure
        $prizeFields = [
            'prize_special', // ĐB
            'prize_1',       // G1
            'prize_2',       // G2
            'prize_3',       // G3
            'prize_4',       // G4
            'prize_5',       // G5
            'prize_6',       // G6
            'prize_7',       // G7
        ];

        foreach ($prizeFields as $field) {
            $prizeValue = $result->$field;

            if ($prizeValue) {
                // Split by comma and extract last 2 digits from each number
                $prizes = explode(',', $prizeValue);
                foreach ($prizes as $prize) {
                    $prize = trim($prize);
                    if (strlen($prize) >= 2) {
                        // Get last 2 digits
                        $twoDigit = substr($prize, -2);
                        $numbers[] = $twoDigit;
                    }
                }
            }
        }

        return $numbers;
    }

    /**
     * Get numbers starting with a specific digit (head)
     *
     * @param \App\Models\LotteryResult $result
     * @param int $digit (0-9)
     * @return string
     */
    public static function getHeadNumbers($result, int $digit): string
    {
        $allNumbers = self::getAllNumbers($result);
        $headNumbers = [];

        foreach ($allNumbers as $number) {
            // Check if the first digit matches
            if (substr($number, 0, 1) == $digit) {
                $headNumbers[] = $number;
            }
        }

        // Remove duplicates and sort
        $headNumbers = array_unique($headNumbers);
        sort($headNumbers);

        return !empty($headNumbers) ? implode(', ', $headNumbers) : '';
    }

    /**
     * Get numbers ending with a specific digit (tail)
     *
     * @param \App\Models\LotteryResult $result
     * @param int $digit (0-9)
     * @return string
     */
    public static function getTailNumbers($result, int $digit): string
    {
        $allNumbers = self::getAllNumbers($result);
        $tailNumbers = [];

        foreach ($allNumbers as $number) {
            // Check if the last digit matches
            if (substr($number, -1) == $digit) {
                $tailNumbers[] = $number;
            }
        }

        // Remove duplicates and sort
        $tailNumbers = array_unique($tailNumbers);
        sort($tailNumbers);

        return !empty($tailNumbers) ? implode(', ', $tailNumbers) : '';
    }

    /**
     * Get count of numbers starting with a specific digit
     *
     * @param \App\Models\LotteryResult $result
     * @param int $digit
     * @return int
     */
    public static function getHeadCount($result, int $digit): int
    {
        $allNumbers = self::getAllNumbers($result);
        $count = 0;

        foreach ($allNumbers as $number) {
            if (substr($number, 0, 1) == $digit) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get count of numbers ending with a specific digit
     *
     * @param \App\Models\LotteryResult $result
     * @param int $digit
     * @return int
     */
    public static function getTailCount($result, int $digit): int
    {
        $allNumbers = self::getAllNumbers($result);
        $count = 0;

        foreach ($allNumbers as $number) {
            if (substr($number, -1) == $digit) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get full head/tail analysis table data
     *
     * @param \App\Models\LotteryResult $result
     * @return array
     */
    public static function getHeadTailAnalysis($result): array
    {
        $analysis = [];

        for ($i = 0; $i < 10; $i++) {
            $analysis[] = [
                'digit' => $i,
                'head_numbers' => self::getHeadNumbers($result, $i),
                'head_count' => self::getHeadCount($result, $i),
                'tail_numbers' => self::getTailNumbers($result, $i),
                'tail_count' => self::getTailCount($result, $i),
            ];
        }

        return $analysis;
    }
}
