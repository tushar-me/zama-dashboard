<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Website\CollectionResource;
use App\Http\Resources\Website\ProductResource;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $collections = Collection::query()
        ->when($request->has('trending'), function ($query) {
            return $query->where('is_trending', true);
        })
        ->orderBy('order_level')
        ->get();

        return CollectionResource::collection($collections);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $collection = Collection::query()->where("slug", $slug)->first();
        $products = $collection->products()->paginate(20);
        
        return response()->json( [
            'collection' => CollectionResource::make($collection),
            'products' => ProductResource::collection($products),
        ]);
    }
}
