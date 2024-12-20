<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();

        // return response()->json(CategoryResource::collection($categories));
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $category = Category::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category),
            ], 201);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $category->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category),
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        if (!$category->exists) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        try {

            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully',
                'data' => new CategoryResource($category),
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
