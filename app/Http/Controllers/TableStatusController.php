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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TableStatus $tableStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableStatus $tableStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableStatus $tableStatus)
    {
        //
    }
}
