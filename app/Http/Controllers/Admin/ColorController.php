<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use App\Http\Resources\Admin\ColorResource;
use App\Http\Requests\Admin\ColorRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\RedirectResponse;
use App\Models\Scopes\AdminScope;
class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @var AnonymousResourceCollection<ColorResource>
     * @return \Inertia\Response
     */
    public function index() : \Inertia\Response
    {
        $colors = Color::query()->with('creator:id,name','editor:id,name')
        ->search(['name','creator.name','editor.name'], request()->search);
        return inertia('Mockup/Variation/Color', [
            'colors' => ColorResource::collection($colors),
            'search' => request()->search,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param ColorRequest $request
     * @return RedirectResponse
     */
    public function store(ColorRequest $request): RedirectResponse
    {
        Color::query()->create($request->validated());

        return to_route('color.index');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'colors' => 'required|array',
            'colors.*.name' => 'required|string|max:100',
            'colors.*.hex_code' => 'required|string|max:100',
            'colors.*.rgb' => 'nullable|string|max:100',
            'colors.*.status' => 'required|in:0,1',
        ]);

        $data = [];
        foreach ($request->colors as $color) {
            $data[] = [
                'name' => $color['name'],
                'hex_code' => $color['hex_code'],
                'rgb' => $color['rgb'],
                'status' => $color['status'],
            ];
        }
        Color::query()->insert($data);

        return to_route('color.index');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(ColorRequest $request, string $id)
    {
        $color = Color::query()->findOrFail($id);
        $color->update($request->validated());

        return to_route('color.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Color::query()->findOrFail($id)->delete();

        return to_route('color.index');
    }
}
