<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
        $validate = $request->validate([
            'name' => 'required|max:255'
        ]);

        $category = Category::create($validate);

        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        //
    }

    function update(Request $request, Category $category)
    {
        $validate = $request->validate([
            'name' => 'required|max:255'
        ]);

        $category->update($validate);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return new CategoryResource($category);
    }
}
