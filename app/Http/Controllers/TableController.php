<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth:sanctum', 'is_admin'])->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::all();
        return response()->json(['data' => $tables]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required',
        ]);

        $validated['status'] = 'Kosong';

        $table = Table::create($validated);

        return response()->json(['data' => $table]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'url' => 'required',
            'status' => 'required'
        ]);

        $table->update($validated);

        return response()->json(['data' => $table]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json(['data' => $table]);
    }
}
