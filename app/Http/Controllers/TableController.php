<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableController extends Controller
{

    public function index()
    {
        $tables = Table::all();
        return response()->json(['data' => $tables]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'nullable',
        ]);

        if (empty($validated['url'])) {
            $validated['url'] = uniqid();
        }

        $validated['table_status_id'] = 1;

        $table = Table::create($validated);

        return response()->json(['data' => $table]);
    }

    public function show(Table $table)
    {
        if (!$table) {
            return response()->json(['message' => 'table not found'], 404);
        }

        return response()->json($table);
    }


    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'url' => 'nullable',
            'table_status_id' => 'nullable'
        ]);

        $table->update($validated);

        return response()->json(['data' => $table]);
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json(['data' => $table]);
    }
}
