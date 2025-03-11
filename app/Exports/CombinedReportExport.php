<?php

namespace App\Exports;

use App\Models\ReportHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class CombinedReportExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ReportHistory::query()
            ->join('portfolios', 'report_history.portfolio_id', '=', 'portfolios.id') // ✅ Fetch portfolio name
            ->join('mutualfund_master', 'report_history.fundname_id', '=', 'mutualfund_master.id')
            ->select(
                'report_history.id',
                'portfolios.name as portfolio_name', // ✅ Show Portfolio Name
                'mutualfund_master.fundname as fund_name',
                'report_history.date as transaction_date',
                'report_history.status as type', // 1 = Buy, 0 = Sell
                'report_history.unit as quantity_of_shares',
                'report_history.price as price_per_unit',
                DB::raw('(report_history.unit * report_history.price) AS total_price')
            );

        // Filter by Portfolio ID (if provided)
        if (!empty($this->filters['portfolio_id'])) {
            $query->where('report_history.portfolio_id', $this->filters['portfolio_id']);
        }

        // Filter by Date Range (if provided)
        if (!empty($this->filters['date_range'])) {
            [$startDate, $endDate] = explode(' - ', $this->filters['date_range']);
            $query->whereBetween('report_history.date', [$startDate, $endDate]);
        }

        // Convert 'status' to 'Buy' or 'Sell'
        $data = $query->get()->map(function ($item) {
            $item->type = $item->type == 1 ? 'Buy' : 'Sell'; // ✅ Converts 1/0 to 'Buy'/'Sell'
            return $item;
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Portfolio Name',  // ✅ Display Portfolio Name instead of ID
            'Fund Name',
            'Transaction Date',
            'Type', // ✅ Corrected for 'Buy' or 'Sell'
            'Quantity of Shares',
            'Price Per Unit',
            'Total Price'
        ];
    }
}
