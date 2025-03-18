<?php

namespace App\Imports;

use App\Models\Sample;
use App\Models\MutualFund_Master;
use App\Models\Portfolio;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FundImport implements ToModel, WithHeadingRow
{
    protected $userId;

    // Accept user ID in the constructor
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
        // Convert all keys to lowercase to prevent case mismatches
        $row = array_change_key_case($row, CASE_LOWER);

        // Log available keys for debugging
        Log::info('Excel Column Keys:', array_keys($row));

        // Log the row data for debugging
        Log::info('Imported Row:', $row);

        // Ensure correct price key
        $price = $row['price'] ?? ($row['price_per_unit'] ?? null);
        
        // Log price value
        Log::info('Price Value:', ['price' => $price]);

        // Find portfolio by name in portfolios table
        $portfolio = Portfolio::whereRaw("TRIM(name) = ?", [trim($row['portfolio'])])->first();

        // Find fund by name in mutualfund_master table
        $fund = MutualFund_Master::whereRaw("TRIM(fundname) = ?", [trim($row['fund_name'])])->first();

        // Identify correct buy/sale column key
        $buySaleKey = array_key_exists('buy/sale', $row) ? 'buy/sale' : (array_key_exists('status', $row) ? 'status' : null);
        $buySaleValue = isset($row[$buySaleKey]) ? strtolower(trim($row[$buySaleKey])) : '';

        // Determine status
        if (!empty($buySaleValue)) {
            $status = ($buySaleValue === 'buy') ? 1 : 0;
        } else {
            Log::error('Buy/Sale column missing or empty:', ['row' => $row]);
            $status = null; // Handle missing status properly
        }

        // Log the final status determination
        Log::info('Sanitized Buy/Sale Value:', ['value' => $buySaleValue]);
        Log::info('Final Determined Status:', ['status' => $status]);

        // Handle incorrect or missing date formats
        try {
            $formattedDate = Carbon::parse($row['date'])->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('Date parsing error:', ['date' => $row['date'], 'error' => $e->getMessage()]);
            $formattedDate = null; // Set to NULL if date is invalid
        }

        return new Sample([
            'user_id'        => $this->userId,  // Assign the logged-in user
            'portfolio_id'   => $portfolio ? $portfolio->id : null, 
            'fundname_id'    => $fund ? $fund->id : null, 
            'date'           => $formattedDate,
            'unit'           => $row['unit'] ?? null,  // ✅ Added the 'unit' column
            'price'          => $price, // Ensure correct key name
            'total'          => $row['total'] ?? null,
            'status'         => $status, // Store status correctly
        ]);
    }
}
