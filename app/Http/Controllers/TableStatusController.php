<?php

namespace App\Http\Controllers;

use App\Models\TableStatus;

class TableStatusController extends Controller
{
    public function index()
    {
        $tableStatuses = TableStatus::all();

        return response()->json(['data' => $tableStatuses]);
    }
}
