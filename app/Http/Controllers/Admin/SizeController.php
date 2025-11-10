<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;
use App\Http\Resources\Admin\SizeResource;
use App\Http\Requests\Admin\SizeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @var AnonymousResourceCollection<SizeResource>
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $sizes = Size::query()
        ->with(['creator:id,name', 'editor:id,name'])
        ->orderBy('order_level')
        ->search(['name', 'creator.name', 'editor.name'], request()->search);
        return inertia('Product/Variation/Size', [
            'sizes' => SizeResource::collection($sizes),
            'search' => request()->search,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SizeRequest $request)
    {
        Size::query()->create($request->validated());

        return to_route('size.index');
    }

    public function bulkStore(Request $request)
    {
        $data = [];
        foreach ($request->sizes as $size) {
            $data[] = [
                'name' => $size['name'],
                'status' => $size['status'],
            ];
        }
        Size::query()->insert($data);

        return to_route('size.index');
    }

    /**
     * Update the specified resource in storage.
     * @param SizeRequest $request
     * @param string $id
     * @return RedirectResponse
     * @exception AuthorizationException
     */
    public function update(SizeRequest $request, string $id): RedirectResponse
    {
        $size = Size::query()->findOrFail($id);
        $size->update($request->validated());

        return to_route('size.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return RedirectResponse
     * @exception AuthorizationException
     */
    public function destroy(string $id): RedirectResponse
    {
        Size::query()->findOrFail($id)->delete();

        return to_route('size.index');
    }
}
