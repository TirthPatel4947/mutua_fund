<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\MutualFund_Master;

class DashboardController
{
    public function showInvestmentAmount()
    {
        $userId = auth()->id(); // Get authenticated user ID

        $investmentData = DB::table('report_history')
            ->select(
                'fundname_id',
                DB::raw('SUM(unit) as total_units'),
                DB::raw('SUM(total) as total_investment')
            )
            ->where('user_id', $userId) // Filter by user_id
            ->groupBy('fundname_id')
            ->get();

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

        $investmentAmount = (float)$totalInvestment;
        $profitOrLoss = $currentValue - $investmentAmount;
        $absoluteProfitOrLoss = abs($profitOrLoss);
        $profitOrLossLabel = $profitOrLoss > 0 ? 'Profit' : 'Loss';

        $percentageGain = $investmentAmount > 0
            ? (($currentValue - $investmentAmount) / $investmentAmount) * 100
            : 0.0;

        $formattedPercentageGain = number_format($percentageGain, 2);
        if ($percentageGain > 0) {
            $formattedPercentageGain = '+' . $formattedPercentageGain;
        }

        return view('dashboard', [
            'totalInvestment' => number_format($totalInvestment, 2, '.', ''),
            'currentValue' => number_format(round($currentValue, 2), 0, '.', ''),
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
            $userId = auth()->id(); 
            $portfolioId = request()->get('portfolio_id');
    
            // Fetch total investment per fund with NAV at purchase time
            $investmentQuery = DB::table('report_history')
                ->select(
                    'fundname_id',
                    DB::raw('SUM(unit) as total_units'),
                    DB::raw('SUM(total) as total_cost'),
                    DB::raw('SUM(unit * price) / SUM(unit) as cost_per_unit') // Weighted average cost per unit
                )
                ->where('user_id', $userId);
    
            if ($portfolioId && $portfolioId !== 'all') {
                $investmentQuery->where('portfolio_id', $portfolioId);
            }
    
            $investmentData = $investmentQuery->groupBy('fundname_id')->get();
    
            // Fetch latest NAVs
            $latestNavs = DB::table('mutual_nav_history as nav')
                ->select('nav.fundname_id', 'nav.nav', 'nav.date')
                ->whereIn('nav.fundname_id', $investmentData->pluck('fundname_id')->toArray())
                ->whereRaw('nav.date = (SELECT MAX(date) FROM mutual_nav_history WHERE fundname_id = nav.fundname_id)')
                ->get()
                ->keyBy('fundname_id');
    
            $fundDetails = [];
            foreach ($investmentData as $data) {
                $units = (float)$data->total_units;
                $investment = (float)$data->total_cost;
                $costPerUnit = (float)$data->cost_per_unit; // Now correctly computed from NAV at investment time
                $lastNav = isset($latestNavs[$data->fundname_id]) ? (float)$latestNavs[$data->fundname_id]->nav : 0.0;
                $lastNavDate = isset($latestNavs[$data->fundname_id]) ? $latestNavs[$data->fundname_id]->date : 'N/A';
                $currentValue = $units * $lastNav;
                $percentageGain = $investment > 0 ? (($currentValue - $investment) / $investment) * 100 : 0.0;
    
                $fundName = DB::table('mutualfund_master')
                    ->where('id', $data->fundname_id)
                    ->value('fundname');
    
                $fundDetails[] = [
                    'fund_name' => $fundName,
                    'total_cost' => number_format($investment, 2),
                    'cost_per_unit' => number_format($costPerUnit, 2), // Now correct
                    'total_units' => number_format($units, 2),
                    'current_value' => number_format($currentValue, 2),
                    'total_return' => number_format($percentageGain, 2) . '%',
                    'current_nav' => number_format($lastNav, 2),
                    'nav_date' => $lastNavDate
                ];
            }
    
            return DataTables::of($fundDetails)->make(true);
        }
    
        $portfolios = DB::table('portfolios')->where('user_id', auth()->id())->get();
        return view('fund-details', compact('portfolios'));
    }
    
    
    
    // for chart 
    public function getInvestmentData()
    {
        $userId = auth()->id(); // Get authenticated user ID

        // Fetch yearly investment data for the authenticated user
        // Fetch yearly investment (BUY) data for the authenticated user
        $yearlyInvestment = DB::table('report_history')
            ->select(DB::raw('YEAR(date) as year'), DB::raw('SUM(total) as total_investment'))
            ->where('user_id', $userId) // Filter by user_id
            ->where('status', 1) // Ensure only 'buy' transactions
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year', 'ASC')
            ->get();


        $years = $yearlyInvestment->pluck('year')->toArray();
        $investmentValues = $yearlyInvestment->pluck('total_investment')->toArray();

        // Fetch yearly sales data for the authenticated user
        $yearlySales = DB::table('report_history')
            ->select(DB::raw('YEAR(date) as year'), DB::raw('SUM(total) as total_sales'))
            ->where('status', '<>', 1) // Exclude status = 1
            ->where('user_id', $userId) // Filter by user_id
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year', 'ASC')
            ->get();

        $salesYears = $yearlySales->pluck('year')->toArray();
        $salesValues = $yearlySales->pluck('total_sales')->toArray();

        // Fetch fund investment data for the authenticated user
        $fundInvestmentData = DB::table('report_history')
            ->select('mutualfund_master.fundname AS fund_name', DB::raw('SUM(report_history.price) AS total_investment'))
            ->join('mutualfund_master', 'report_history.fundname_id', '=', 'mutualfund_master.id')
            ->where('report_history.user_id', $userId) // Filter by user_id
            ->groupBy('mutualfund_master.fundname')
            ->get();

        $fundNames = $fundInvestmentData->pluck('fund_name')->toArray();
        $fundInvestments = $fundInvestmentData->pluck('total_investment')->toArray();

        // Return response with all filtered data
        return response()->json([
            'years' => $years,
            'investmentValues' => $investmentValues,
            'salesYears' => $salesYears, // Ensure frontend knows which years match sales data
            'salesValues' => $salesValues,
            'fundNames' => $fundNames,
            'fundInvestments' => $fundInvestments
        ]);
    }
    public function fetchTopData()
    {
        $funds = DB::select("
            SELECT 
                mf.fundname,
                FORMAT(IFNULL(nh1.nav, 0), 2) AS old_nav,
                FORMAT(IFNULL(nh2.nav, 0), 2) AS new_nav,
                IFNULL(
                    SUM(CASE WHEN rh.status = 1 THEN rh.unit ELSE 0 END) - 
                    SUM(CASE WHEN rh.status = 0 THEN rh.unit ELSE 0 END), 
                    0
                ) AS current_units,
                ROUND(
                    (IFNULL(nh2.nav, 0) - IFNULL(nh1.nav, 0)) * 
                    IFNULL(
                        (SUM(CASE WHEN rh.status = 1 THEN rh.unit ELSE 0 END) - 
                         SUM(CASE WHEN rh.status = 0 THEN rh.unit ELSE 0 END)
                        ), 
                    0)
                , 0) AS difference,  -- ✅ Rounded to whole number
                IFNULL(((nh2.nav - nh1.nav) / nh1.nav) * 100, 2) AS percentage_change -- ✅ Keeps decimal places
            FROM 
                mutualfund_master AS mf
            LEFT JOIN 
                mutual_nav_history AS nh1 
                ON mf.id = nh1.fundname_id
                AND nh1.date = (
                    SELECT MAX(date) 
                    FROM mutual_nav_history 
                    WHERE fundname_id = nh1.fundname_id
                    AND date < (
                        SELECT MAX(date) 
                        FROM mutual_nav_history
                    )
                )
            LEFT JOIN 
                mutual_nav_history AS nh2 
                ON mf.id = nh2.fundname_id
                AND nh2.date = (
                    SELECT MAX(date) 
                    FROM mutual_nav_history
                )
            LEFT JOIN 
                report_history AS rh 
                ON mf.id = rh.fundname_id
                AND (rh.user_id = :user_id OR rh.user_id IS NULL)
            GROUP BY 
                mf.fundname, nh1.nav, nh2.nav
            ORDER BY 
                difference DESC
        ", ['user_id' => auth()->id()]);
    
        // Convert 'difference' to integer for correct display
        $funds = collect($funds)->map(function ($fund) {
            $fund->difference = (int) $fund->difference; // ✅ Convert to integer
            $fund->percentage_change = round($fund->percentage_change, 2); // ✅ Keep 2 decimal places
            return $fund;
        });
    
        // Show Top 3 Gainers and Top 3 Losers
        $topGainers = $funds->where('difference', '>', 0)->sortByDesc('difference')->take(3)->values();
        $topLosers = $funds->where('difference', '<', 0)->sortBy('difference')->take(3)->values();
    
        return response()->json([
            'topGainers' => $topGainers,
            'topLosers' => $topLosers
        ]);
    }
    
}
