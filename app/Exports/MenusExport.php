<?php

namespace App\Exports;

use App\Http\Resources\MenuReportResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MenusExport implements FromView
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
        $menuReports = MenuReportResource::collection($this->reports)->toArray(request());
        $date = $this->date;

        return view('reports.menus.excel', compact('menuReports', 'date'));
    }
}
