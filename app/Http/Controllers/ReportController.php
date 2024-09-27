<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuReportResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\TableReportResource;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = OrderItem::orderItemReports($startDate, $endDate);
        return ReportResource::collection($reports);
        // return response()->json($reports);
    }

    public function menuReports(Request $request)
    {
        $valdatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
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
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Table::tableReports($startDate, $endDate);
        return TableReportResource::collection($reports);
    }

    public function orderReports(Request $request)
    {
        $startDate = $valdatedData['start_date'] ?? null;
        $endDate = $valdatedData['end_date'] ?? null;

        $reports = Transaction::orderReports($startDate, $endDate);
        return response()->json(['data' => $reports]);
    }
}
