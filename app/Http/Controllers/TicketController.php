<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\LotteryResult;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function verify(Request $request)
    {
        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        // Get grouped provinces for sidebars
        $northProvinces = Province::where('region', 'north')->where('is_active', true)->orderBy('name')->get();
        $centralProvinces = Province::where('region', 'central')->where('is_active', true)->orderBy('name')->get();
        $southProvinces = Province::where('region', 'south')->where('is_active', true)->orderBy('name')->get();

        $result = null;
        $matchedPrizes = [];
        $ticketNumber = null;
        $selectedDate = null;
        $selectedProvinceId = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'ticket_number' => 'required|string|min:2|max:6',
                'draw_date' => 'required|date',
                'province_id' => 'required|exists:provinces,id',
            ]);

            $ticketNumber = $request->input('ticket_number');
            $selectedDate = Carbon::parse($request->input('draw_date'));
            $selectedProvinceId = $request->input('province_id');

            // Find the lottery result
            $result = LotteryResult::where('province_id', $selectedProvinceId)
                ->whereDate('draw_date', $selectedDate)
                ->first();

            if ($result) {
                // Check ticket number against all prizes
                $ticketDigits = strlen($ticketNumber);

                // Check each prize tier
                $prizes = [
                    'special' => ['name' => 'Giải Đặc Biệt', 'value' => $result->prize_special, 'amount' => '2,000,000,000 đ'],
                    '1' => ['name' => 'Giải Nhất', 'value' => $result->prize_1, 'amount' => '200,000,000 đ'],
                    '2' => ['name' => 'Giải Nhì', 'value' => $result->prize_2, 'amount' => '100,000,000 đ'],
                    '3' => ['name' => 'Giải Ba', 'value' => $result->prize_3, 'amount' => '30,000,000 đ'],
                    '4' => ['name' => 'Giải Tư', 'value' => $result->prize_4, 'amount' => '4,000,000 đ'],
                    '5' => ['name' => 'Giải Năm', 'value' => $result->prize_5, 'amount' => '1,000,000 đ'],
                    '6' => ['name' => 'Giải Sáu', 'value' => $result->prize_6, 'amount' => '400,000 đ'],
                    '7' => ['name' => 'Giải Bảy', 'value' => $result->prize_7, 'amount' => '200,000 đ'],
                ];

                if ($result->prize_8) {
                    $prizes['8'] = ['name' => 'Giải Tám', 'value' => $result->prize_8, 'amount' => '100,000 đ'];
                }

                foreach ($prizes as $key => $prize) {
                    if (!$prize['value']) continue;

                    // Handle comma-separated prizes
                    $prizeNumbers = explode(',', $prize['value']);

                    foreach ($prizeNumbers as $prizeNumber) {
                        $prizeNumber = trim($prizeNumber);

                        // Check if ticket matches (last N digits)
                        if (strlen($prizeNumber) >= $ticketDigits) {
                            $lastDigits = substr($prizeNumber, -$ticketDigits);

                            if ($lastDigits === $ticketNumber) {
                                $matchedPrizes[] = [
                                    'tier' => $prize['name'],
                                    'number' => $prizeNumber,
                                    'amount' => $prize['amount'],
                                ];
                            }
                        }
                    }
                }
            }
        }

        return view('ticket-verify', compact(
            'provinces',
            'northProvinces',
            'centralProvinces',
            'southProvinces',
            'result',
            'matchedPrizes',
            'ticketNumber',
            'selectedDate',
            'selectedProvinceId'
        ));
    }
}
