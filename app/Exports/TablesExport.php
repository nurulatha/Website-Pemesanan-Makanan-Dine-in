<?php

namespace App\Exports;

use App\Http\Resources\TableReportResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TablesExport implements FromView
{
    protected $reports;

    protected $date;

    public function __construct($reports, $date)
    {
        $this->reports = $reports;
        $this->date = $date;
    }

    public function view(): View
    {
        $tableReports = TableReportResource::collection($this->reports)->toArray(request());
        $date = $this->date;

        return view('reports.tables.excel', compact('tableReports', 'date'));
    }
}
