<?php

namespace App\Http\Controllers;

use App\Models\TableStatus;
use Illuminate\Http\Request;

class TableStatusController extends Controller
{
    public function index()
    {
        $tableStatuses = TableStatus::all();
        return response()->json(['data' => $tableStatuses]);
    }
}
