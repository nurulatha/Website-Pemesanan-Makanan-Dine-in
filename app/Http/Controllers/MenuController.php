<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();

        return response()->json(
            MenuResource::collection($menus->loadMissing('category'))
        );

        // return MenuResource::collection($menus->loadMissing('category'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            if ($request->hasFile('image')) {

                $fileImage = $request->file('image');
                $fileName = $request->category_id.'_'.(string) Str::uuid().'.'.$fileImage->extension();

                $image = Storage::putFileAs('images', $fileImage, $fileName);
            }

            $menu = Menu::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image ?? 'images/noimage.jpg',
                'price' => $request->price,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Menu created successfully',
                'data' => new MenuResource($menu->loadMissing('category:id,name')),
            ], 201);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            if ($request->hasFile('image')) {

                if ($menu->image) {
                    Storage::delete($menu->image);
                }

                $fileImage = $request->file('image');
                $fileName = $request->category_id.'_'.(string) Str::uuid().'.'.$fileImage->extension();

                $image = Storage::putFileAs('images', $fileImage, $fileName);
            } else {

                if ($menu->category_id != $request->category_id) {

                    $oldFileName = $menu->image;
                    $fileExtension = pathinfo($oldFileName, PATHINFO_EXTENSION);
                    $newFileName = $request->category_id.'_'.(string) Str::uuid().'.'.$fileExtension;

                    if (Storage::move($oldFileName, 'images/'.$newFileName)) {
                        $image = 'images/'.$newFileName;
                    } else {
                        $image = $oldFileName;
                    }
                }
            }

            $menu->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image ?? $menu->image,
                'price' => $request->price,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Menu updated successfully',
                'data' => new MenuResource($menu->loadMissing('category:id,name')),
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function destroy(Menu $menu)
    {
        if (! $menu->exists) {
            return response()->json([
                'status' => false,
                'message' => 'Menu not found',
            ], 404);
        }

        try {

            if ($menu->image) {
                Storage::delete($menu->image);
            }

            $menu->delete();

            return response()->json([
                'status' => true,
                'message' => 'Menu deleted successfully',
                'data' => new MenuResource($menu->loadMissing('category:id,name')),
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
