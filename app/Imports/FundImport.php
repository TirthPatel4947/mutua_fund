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
    
        // Log the row data
        Log::info('Imported Row:', $row);
    
        // Ensure correct price key
        $price = $row['price'] ?? ($row['price_per_unit'] ?? null);
    
        // Log price value
        Log::info('Price Value:', ['price' => $price]);
    
        // Find portfolio by name in portfolios table
        $portfolio = Portfolio::whereRaw("TRIM(name) = ?", [trim($row['portfolio'])])->first();
    
        // Find fund by name in mutualfund_master table
        $fund = MutualFund_Master::whereRaw("TRIM(fundname) = ?", [trim($row['fund_name'])])->first();
    
        return new Sample([
            'user_id'        => $this->userId,  // Assign the logged-in user
            'portfolio_id'   => $portfolio ? $portfolio->id : null, 
            'fundname_id'    => $fund ? $fund->id : null, 
            'date'           => Carbon::parse($row['date'])->format('Y-m-d'),
            'price'          => $price, // Ensure correct key name
            'total'          => $row['total'],
        ]);
    }
}
