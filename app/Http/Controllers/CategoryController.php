<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware(['auth:sanctum'])->only(['store', 'update', 'destroy']);
    // }

    public function index()
    {
        $categories = Category::all();

        // return response()->json(CategoryResource::collection($categories));
        return CategoryResource::collection($categories);
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
        $validate = $request->validate([
            'name' => 'required|max:255'
        ]);

        $category = Category::create($validate);

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return new CategoryResource($category);
    }
}
