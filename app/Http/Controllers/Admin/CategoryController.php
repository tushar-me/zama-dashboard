<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FileUploadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): \Inertia\Response
    {
        $categories = Category::query()
        ->orderBy('order_level')
        ->with('creator:id,name', 'editor:id,name','shippingCharge')
        ->search(['name','creator.name','editor.name'],$request->search);
        return inertia('Category/Index', [
            'categories' => CategoryResource::collection($categories),
            'per_page' => $request->per_page,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Category/Create', [
            'parent_categories' => Category::query()->select(['id', 'name'])->where('parent_id', 0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param CategoryRequest $request
     * @return \Inertia\Response
     */
    public function store(CategoryRequest $request, FileUploadAction $fileAction) : RedirectResponse
    {
        $category = Category::query()->create([
            ...$request->validated(),
            'image' => $request->file('image') ? $fileAction->upload($request->file('image')) : null,
        ]);
       $category->shippingCharge()->create([
            'us_charge' => $request->input('us_charge') ?? 0,
            'us_add_charge_per_item' => $request->input('us_add_charge_per_item') ?? 0,
            'worldwide_charge' => $request->input('worldwide_charge')?? 0,
            'worldwide_add_charge_per_item' => $request->input('worldwide_add_charge_per_item')?? 0
        ]);

        return to_route('category.index');
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
        $category = Category::query()->with('shippingCharge')->findOrFail($id);
        $parentCategories = Category::query()->select(['id', 'name'])->where('parent_id', null)->get();
        
        return inertia('Category/Edit', [
            'category' => $category,
            'parent_categories' => $parentCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param CategoryRequest $request
     * @param Category $category
     * @param FileUploadAction $fileAction
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category, FileUploadAction $fileAction): RedirectResponse
    {
        $category->update([
            ...$request->validated(),
            'image' => $request->file('image') ? $fileAction->upload($request->file('image'), $category->image) : $category->getRawOriginal('image'),
        ]);
        $category->shippingCharge()->update([
            'us_charge' => $request->input('us_charge'),
            'us_add_charge_per_item' => $request->input('us_add_charge_per_item'),
            'worldwide_charge' => $request->input('worldwide_charge'),
            'worldwide_add_charge_per_item' => $request->input('worldwide_add_charge_per_item')
        ]);
        return to_route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $category = Category::query()->findOrFail($id);
        if($category->image){
            Storage::disk(env('FILESYSTEM_DISK'))->delete($category->image);
        }
        $category->delete();

        return to_route('category.index');
    }
}
