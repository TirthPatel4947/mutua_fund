<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\MutualFund_Master;

class DashboardController
{
    public function showInvestmentAmount()
    {
        // Step 1: Get total units and investment grouped by fundname_id
        $investmentData = DB::table('report_history')
            ->select(
                'fundname_id',
                DB::raw('SUM(unit) as total_units'),
                DB::raw('SUM(price) as total_investment')
            )
            ->whereNull('user-id')
            ->groupBy('fundname_id')
            ->get();

        // Step 2: Get the latest NAV for each fundname_id
        $latestNavs = DB::table('mutual_nav_history as nav')
            ->whereIn('nav.fundname_id', $investmentData->pluck('fundname_id')->toArray())
            ->whereRaw('nav.date = (
                SELECT MAX(date) 
                FROM mutual_nav_history 
                WHERE fundname_id = nav.fundname_id
            )')
            ->pluck('nav', 'fundname_id')
            ->map(function ($value) {
                // Ensure each NAV is a float or 0.0 if not numeric
                return is_numeric($value) ? (float)$value : 0.0;
            });

        // Step 3: Calculate totals
        $totalUnits = 0.0;
        $totalInvestment = 0.0;
        $currentValue = 0.0;

        foreach ($investmentData as $data) {
            // Safely cast units and investments to float, or set to 0.0 if null
            $units = is_numeric($data->total_units) ? (float)$data->total_units : 0.0;
            $investment = is_numeric($data->total_investment) ? (float)$data->total_investment : 0.0;

            $totalUnits += $units;
            $totalInvestment += $investment;

            // Get the last NAV for the current fundname_id
            $lastNav = $latestNavs[$data->fundname_id] ?? 0.0;
            $currentValue += $units * $lastNav;
        }

        // Step 4: Calculate profit/loss and percentage change
        $investmentAmount = (float)$totalInvestment;
        $profitOrLoss = $currentValue - $investmentAmount;

        // Calculate absolute profit/loss
        $absoluteProfitOrLoss = abs($profitOrLoss);

        // Determine if it's a profit or a loss
        $profitOrLossLabel = $profitOrLoss > 0 ? 'Profit' : 'Loss';

        // Percentage Calculation Formula:
        $percentageGain = $investmentAmount > 0
            ? (($currentValue - $investmentAmount) / $investmentAmount) * 100
            : 0.0;

        // Format percentage to show + or - explicitly
        $formattedPercentageGain = '';
        if ($percentageGain > 0) {
            $formattedPercentageGain = '+' . number_format($percentageGain, 2) . '%';
        } elseif ($percentageGain < 0) {
            $formattedPercentageGain = number_format($percentageGain, 2) . '%';
        } else {
            $formattedPercentageGain = '0.00%';
        }

        //mohit
        // Step 5: Pass the data to the view, ensuring all values are numeric
        return view('dashboard', [
            'totalInvestment' => number_format($totalInvestment, 2, '.', ''),
            'currentValue' => number_format($currentValue, 2, '.', ''),
            'totalUnits' => number_format($totalUnits, 2, '.', ''),
            'profitOrLoss' => number_format($profitOrLoss, 2, '.', ''),
            'absoluteProfitOrLoss' => number_format($absoluteProfitOrLoss, 2, '.', ''), // Display absolute value
            'profitOrLossLabel' => $profitOrLossLabel, // Display if it's profit or loss
            'percentageGain' => $formattedPercentageGain,
            'investmentAmount' => number_format($investmentAmount, 2, '.', '')
        ]);
    }
    public function fundDetails()
    {
        // Step 1: Get total units and investment grouped by fundname_id
        $investmentData = DB::table('report_history')
            ->select(
                'fundname_id',
                DB::raw('SUM(unit) as total_units'),
                DB::raw('SUM(price) as total_investment')
            )
            ->whereNull('user-id')
            ->groupBy('fundname_id')
            ->get();
    
        // Step 2: Get the latest NAV for each fundname_id
        $latestNavs = DB::table('mutual_nav_history as nav')
            ->whereIn('nav.fundname_id', $investmentData->pluck('fundname_id')->toArray())
            ->whereRaw('nav.date = (
                SELECT MAX(date) 
                FROM mutual_nav_history 
                WHERE fundname_id = nav.fundname_id
            )')
            ->pluck('nav.nav', 'fundname_id')
            ->map(function ($value) {
                return is_numeric($value) ? (float)$value : 0.0;
            });
    
        // Step 3: Calculate fund-wise details
        $fundDetails = [];
        foreach ($investmentData as $data) {
            $units = is_numeric($data->total_units) ? (float)$data->total_units : 0.0;
            $investment = is_numeric($data->total_investment) ? (float)$data->total_investment : 0.0;
    
            $lastNav = $latestNavs[$data->fundname_id] ?? 0.0;
            $currentValue = $units * $lastNav;
            $profitOrLoss = $currentValue - $investment;
    
            // Calculate absolute profit/loss
            $absoluteProfitOrLoss = abs($profitOrLoss);
    
            // Determine if it's a profit or a loss
            $profitOrLossLabel = $profitOrLoss > 0 ? 'Profit' : 'Loss';
    
            // Percentage Calculation
            $percentageGain = $investment > 0
                ? (($currentValue - $investment) / $investment) * 100
                : 0.0;
    
            $formattedPercentageGain = '';
            if ($percentageGain > 0) {
                $formattedPercentageGain = '+' . number_format($percentageGain, 2) . '%';
            } elseif ($percentageGain < 0) {
                $formattedPercentageGain = number_format($percentageGain, 2) . '%';
            } else {
                $formattedPercentageGain = '0.00%';
            }
    
            // Get fund name from the correct column and table
            $fundName = DB::table('mutualfund_master')->where('id', $data->fundname_id)->value('fundname');
    
            // Ensure fund name exists and is not null
            if ($fundName) {
                $fundDetails[] = [
                    'fund_name' => $fundName,
                    'total_units' => number_format($units, 2),
                    'total_investment' => number_format($investment, 2),
                    'current_value' => number_format($currentValue, 2),
                    'profit_or_loss_label' => $profitOrLossLabel,
                    'absolute_profit_or_loss' => number_format($absoluteProfitOrLoss, 2),
                    'percentage_gain' => $formattedPercentageGain,
                    'current_nav' => $lastNav, // Add the current NAV for unit-wise details
                ];
            }
        }
    
        // Step 4: Pass data to the view
        return view('fund-details', compact('fundDetails'));
    }
    
}
