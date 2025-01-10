<?php

namespace App\Http\Controllers;

use App\Exports\MenusExport;
use App\Exports\TablesExport;
use App\Http\Resources\MenuReportResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\TableReportResource;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // public function index(Request $request)
    // {
    //     $valdatedData = $request->validate([
    //         'start_date' => 'nullable|date',
    //         'end_date' => 'nullable|date|after_or_equal:start_date',
    //     ]);

    //     $startDate = $valdatedData['start_date'] ?? null;
    //     $endDate = $valdatedData['end_date'] ?? null;

    //     $reports = OrderItem::orderItemReports($startDate, $endDate);
    //     return ReportResource::collection($reports);
    // }

    // public function orderPaidReports(Request $request)
    // {
    //     $valdatedData = $request->validate([
    //         'start_date' => 'nullable|date',
    //         'end_date' => 'nullable|date|after_or_equal:start_date'
    //     ]);

    //     $startDate = $valdatedData['start_date'] ?? null;
    //     $endDate = $valdatedData['end_date'] ?? null;

    //     $reports = Order::orderPaidReports($startDate, $endDate);
    //     return response()->json(['data' => $reports]);
    // }

    public function menuReports(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Menu::menuReports($startDate, $endDate);

        return MenuReportResource::collection($reports);
    }

    public function tableReports(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Table::tableReports($startDate, $endDate);

        return TableReportResource::collection($reports);
    }

    public function menuReportsExcel(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Menu::menuReports($startDate, $endDate);

        $dates = $this->getDateFormats($startDate, $endDate);

        return Excel::download(new MenusExport($reports, $dates['dateViewFile']), 'menu_report_'.$dates['dateFileName'].'.xlsx');
    }

    public function menuReportsPdf(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Menu::menuReports($startDate, $endDate);
        $menuReports = MenuReportResource::collection($reports)->toArray(request());

        $dates = $this->getDateFormats($startDate, $endDate);

        $pdf = Pdf::loadView('reports.menus.pdf', [
            'menuReports' => $menuReports,
            'date' => $dates['dateViewFile'],
        ]);

        return $pdf->download('menu_report_'.$dates['dateFileName'].'.pdf');
    }

    public function tableReportsExcel(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Table::tableReports($startDate, $endDate);

        $dates = $this->getDateFormats($startDate, $endDate);

        return Excel::download(new TablesExport($reports, $dates['dateViewFile']), 'table_report_'.$dates['dateFileName'].'.xlsx');
    }

    public function tableReportsPdf(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Table::tableReports($startDate, $endDate);
        $tableReports = TableReportResource::collection($reports)->toArray(request());

        $dates = $this->getDateFormats($startDate, $endDate);

        $pdf = Pdf::loadView('reports.tables.pdf', [
            'tableReports' => $tableReports,
            'date' => $dates['dateViewFile'],
        ]);

        return $pdf->download('table_report_'.$dates['dateFileName'].'.pdf');
    }

    protected function getDateFormats(?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {

            if ($startDate === $endDate) {
                $dateFileName = Carbon::parse($startDate)->format('Ymd');
                $dateViewFile = Carbon::parse($startDate)->format('j F Y');
            } else {
                $dateFileName = Carbon::parse($startDate)->format('Ymd').'-'.Carbon::parse($endDate)->format('Ymd');
                $dateViewFile = Carbon::parse($startDate)->format('j F Y').' - '.Carbon::parse($endDate)->format('j F Y');
            }
        } elseif ($startDate) {

            $dateFileName = Carbon::parse($startDate)->format('Ymd').'-'.Carbon::now()->format('Ymd');
            $dateViewFile = Carbon::parse($startDate)->format('j F Y').' - '.Carbon::now()->format('j F Y');
        } elseif ($endDate) {

            $dateFileName = Carbon::parse($endDate)->format('Ymd');
            $dateViewFile = Carbon::parse($endDate)->format('j F Y');
        } else {

            $dateFileName = 'all-data-until-'.Carbon::now()->format('Ymd');
            $dateViewFile = 'All Data Until '.Carbon::now()->format('j F Y');
        }

        return compact('dateFileName', 'dateViewFile');
    }
}
