<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Resources\Store\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() :  AnonymousResourceCollection
    {
        $products = Product::query()
        ->where('is_deleted', false)
        ->where('store_id', request()->header('store'))
        ->where('campaign_id', request()->campaign)
        ->with(['productSides','colors:id,name,hex_code,product_id,color_id,is_default','images','mockup:id,name','mockup.colors:id,name,hex_code'])
        ->paginate(20);

        return ProductResource::collection($products);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::query()->with('colors','colors.images')->findOrFail($id);
        return ProductResource::make($product);
    }
}
