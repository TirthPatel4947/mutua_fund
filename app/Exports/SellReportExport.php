<?php

namespace App\Exports;

use App\Models\ReportHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SellReportExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ReportHistory::query()
            ->join('portfolios', 'report_history.portfolio_id', '=', 'portfolios.id')
            ->join('mutualfund_master', 'report_history.fundname_id', '=', 'mutualfund_master.id')
            ->select(
                'portfolios.name as portfolio_name',
                'mutualfund_master.fundname as fund_name',
                'report_history.date as selling_date', 
                'report_history.unit',
                'report_history.price',
                DB::raw('(report_history.unit * report_history.price) AS total_price')
            )
            ->where('report_history.status', 0); 

        // Filter by Portfolio ID (if provided)
        if (!empty($this->filters['portfolio_id'])) {
            $query->where('report_history.portfolio_id', $this->filters['portfolio_id']);
        }

        // Filter by Date Range (if provided)
        if (!empty($this->filters['date_range'])) {
            [$startDate, $endDate] = explode(' - ', $this->filters['date_range']);
            $query->whereBetween('report_history.date', [$startDate, $endDate]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Portfolio Name',
            'Fund Name',
            'Selling Date', 
            'Quantity of Shares',
            'Price Per Unit',
            'Total Price'
        ];
    }
}
