<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FileUploadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CollectionRequest;
use App\Http\Resources\Admin\CollectionResource;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collections = Collection::query()->search(['name'], request()->search);
        return inertia('Collection/Index',[
            'collections' => CollectionResource::collection($collections),
            'search' => request()->search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Collection/Create', [
            'products' => Product::query()->select(['id', 'name', 'cover_image'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionRequest $request, FileUploadAction $fileAction)
    {
        $collection = Collection::query()->create([
            ...$request->validated(),
            'image' => $request->file('image') ? $fileAction->upload($request->file('image'), false, null, 820, 360) : null,
        ]);
        $collection->products()->sync($request->input('product_ids'));

        return to_route('collection.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Collection::query()->findOrFail($id);
        if($category->image){
            Storage::disk(env('FILESYSTEM_DISK'))->delete($category->image);
        }
        $category->delete();

        return to_route('collection.index');
    }
}
