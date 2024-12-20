<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{

    public function index()
    {
        $tables = Table::all();
        return response()->json(['data' => $tables]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'nullable|unique:tables,url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $table = Table::create([
                'url' => $request->url ?? uniqid(),
                'table_status_id' => 1,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Table created successfully',
                'data' => $table,
            ], 201);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function show(Table $table)
    {
        if (!$table->exists) {
            return response()->json([
                'status' => false,
                'message' => 'Table not found',
            ], 404);
        }

        return response()->json($table);
    }


    public function update(Request $request, Table $table)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'nullable|unique:tables,url,' . $table->id,
            'table_status_id' => 'nullable|exists:table_statuses,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $table->update($request->only([
                'url',
                'table_status_id'
            ]));

            return response()->json([
                'status' => true,
                'message' => 'Table updated successfully',
                'data' => $table,
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function updateStatus(Request $request, Table $table) 
    {
        $validator = Validator::make($request->all(), [
            'table_status_id' => 'required|exists:table_statuses,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $table->update([
                'table_status_id' => $request->table_status_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Table status updated successfully',
                'data' => $table,
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function destroy(Table $table)
    {
        if (!$table->exists) {
            return response()->json([
                'status' => false,
                'message' => 'Table not found',
            ], 404);
        }

        try {

            $table->delete();

            return response()->json([
                'status' => true,
                'message' => 'Table deleted successfully',
                'data' => $table,
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
