<?php

namespace App\Imports;

use App\Models\sample;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FundImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new sample([
            'portfolio' => $row['portfolio'],
            'fund_name' => $row['fund_name'],
            'date' => $row['date'],
            'price_per_unit' => $row['price_per_unit'],
            'total' => $row['total'],
        ]);
    }
}
