<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
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
        $validate = $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'image|file|max:2048',
            'price' => 'required'
        ]);

        if ($request->hasFile('image')) {

            $fileImage = $request->file('image');
            $fileName = $validate['category_id'] . '_' . Str::uuid() . '.' . $fileImage->extension();

            $validate['image'] = Storage::putFileAs('images', $fileImage, $fileName);
        }

        $menu = Menu::create($validate);

        return new MenuResource($menu->loadMissing('category:id,name'));
    }

    public function show(Menu $menu)
    {
        //
    }

    public function update(Request $request, Menu $menu)
    {
        $validate = $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'image|file|max:2048',
            'price' => 'required'
        ]);

        if ($request->hasFile('image')) {

            if ($menu->image) {
                Storage::delete($menu->image);
            }

            $fileImage = $request->file('image');
            $fileName = $validate['category_id'] . '_' . Str::uuid() . '.' . $fileImage->extension();

            $validate['image'] = Storage::putFileAs('images', $fileImage, $fileName);
        }

        $menu->update($validate);

        return new MenuResource($menu->loadMissing('category:id,name'));
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image) {
            Storage::delete($menu->image);
        }

        $menu->delete();

        return new MenuResource($menu->loadMissing('category:id,name'));
    }
}
