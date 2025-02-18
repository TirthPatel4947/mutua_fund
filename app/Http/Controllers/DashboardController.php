<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
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
                return is_numeric($value) ? (float)$value : 0.0;
            });

        // Step 3: Calculate totals
        $totalUnits = 0.0;
        $totalInvestment = 0.0;
        $currentValue = 0.0;

        foreach ($investmentData as $data) {
            $units = is_numeric($data->total_units) ? (float)$data->total_units : 0.0;
            $investment = is_numeric($data->total_investment) ? (float)$data->total_investment : 0.0;

            $totalUnits += $units;
            $totalInvestment += $investment;

            $lastNav = $latestNavs[$data->fundname_id] ?? 0.0;
            $currentValue += $units * $lastNav;
        }

        // Step 4: Calculate profit/loss and percentage change
        $investmentAmount = (float)$totalInvestment;
        $profitOrLoss = $currentValue - $investmentAmount;
        $absoluteProfitOrLoss = abs($profitOrLoss);
        $profitOrLossLabel = $profitOrLoss > 0 ? 'Profit' : 'Loss';

        $percentageGain = $investmentAmount > 0
            ? (($currentValue - $investmentAmount) / $investmentAmount) * 100
            : 0.0;

        $formattedPercentageGain = '';
        if ($percentageGain > 0) {
            $formattedPercentageGain = '+' . number_format($percentageGain, 2);
        } elseif ($percentageGain < 0) {
            $formattedPercentageGain = number_format($percentageGain, 2);
        } else {
            $formattedPercentageGain = '0.00';
        }

        return view('dashboard', [
            'totalInvestment' => number_format($totalInvestment, 2, '.', ''),
            'currentValue' => number_format($currentValue, 2, '.', ''),
            'totalUnits' => number_format($totalUnits, 2, '.', ''),
            'profitOrLoss' => number_format($profitOrLoss, 2, '.', ''),
            'absoluteProfitOrLoss' => number_format($absoluteProfitOrLoss, 2, '.', ''),
            'profitOrLossLabel' => $profitOrLossLabel,
            'percentageGain' => $formattedPercentageGain,
            'investmentAmount' => number_format($investmentAmount, 2, '.', '')
        ]);
    }

    public function fundDetails()
    {
        if (request()->ajax()) {
            $investmentData = DB::table('report_history')
                ->select(
                    'fundname_id',
                    DB::raw('SUM(unit) as total_units'),
                    DB::raw('SUM(price) as total_investment')
                )
                ->groupBy('fundname_id')
                ->get();

            $latestNavs = DB::table('mutual_nav_history as nav')
                ->select('nav.fundname_id', 'nav.nav', 'nav.date')
                ->whereIn('nav.fundname_id', $investmentData->pluck('fundname_id')->toArray())
                ->whereRaw('nav.date = (
                    SELECT MAX(date) 
                    FROM mutual_nav_history 
                    WHERE fundname_id = nav.fundname_id
                )')
                ->get()
                ->keyBy('fundname_id');

            $fundDetails = [];
            foreach ($investmentData as $data) {
                $units = (float)$data->total_units;
                $investment = (float)$data->total_investment;
                $lastNav = isset($latestNavs[$data->fundname_id]) ? (float)$latestNavs[$data->fundname_id]->nav : 0.0;
                $lastNavDate = isset($latestNavs[$data->fundname_id]) ? $latestNavs[$data->fundname_id]->date : 'N/A';
                $currentValue = $units * $lastNav;
                $profitOrLoss = $currentValue - $investment;
                $absoluteProfitOrLoss = abs($profitOrLoss);

                $formattedProfitOrLoss = $profitOrLoss > 0
                    ? '+' . number_format($profitOrLoss, 2)
                    : number_format($profitOrLoss, 2);

                $percentageGain = $investment > 0
                    ? (($currentValue - $investment) / $investment) * 100
                    : 0.0;

                $formattedPercentageGain = number_format($percentageGain, 2) . '%';

                $fundName = DB::table('mutualfund_master')
                    ->where('id', $data->fundname_id)
                    ->value('fundname');

                $fundDetails[] = [
                    'fund_name' => $fundName,
                    'total_units' => number_format($units, 2),
                    'total_investment' => number_format($investment, 2),
                    'current_value' => number_format($currentValue, 2),
                    'profit_or_loss' => $formattedProfitOrLoss,
                    'absolute_profit_or_loss' => number_format($absoluteProfitOrLoss, 2),
                    'percentage_gain' => $formattedPercentageGain,
                    'current_nav' => number_format($lastNav, 2),
                    'nav_date' => $lastNavDate
                ];
            }

            return DataTables::of($fundDetails)->make(true);
        }

        return view('fund-details');
    }
    public function getInvestmentData()
    {
        // Fetch yearly investment data
        $yearlyInvestment = DB::table('report_history')
            ->select(DB::raw('YEAR(date) as year'), DB::raw('SUM(price) as total_investment'))
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year', 'ASC')
            ->get();
    
        $years = $yearlyInvestment->pluck('year')->toArray();
        $investmentValues = $yearlyInvestment->pluck('total_investment')->toArray();
    
        // Fetch fund investment data using the query you provided
        $fundInvestmentData = DB::table('report_history')
            ->select('mutualfund_master.fundname AS fund_name', DB::raw('SUM(report_history.price) AS total_investment'))
            ->join('mutualfund_master', 'report_history.fundname_id', '=', 'mutualfund_master.id')
            ->groupBy('mutualfund_master.fundname')
            ->get();
    
        $fundNames = $fundInvestmentData->pluck('fund_name')->toArray();
        $fundInvestments = $fundInvestmentData->pluck('total_investment')->toArray();
    
        // Return the response with the required data for both charts
        return response()->json([
            'years' => $years,
            'investmentValues' => $investmentValues,
            'fundNames' => $fundNames,
            'fundInvestments' => $fundInvestments
        ]);
    }
    

    
}
