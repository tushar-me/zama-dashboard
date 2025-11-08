<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()->with('store:id,name','campaign:id,name')->paginate(20);
        return inertia('Vendor/Product/Index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(ProductRequest $request)
        {
            $data = $request->validated();
            
            // Handle file uploads
            if ($request->has('cover_image') && $request->cover_image) {
                $data['cover_image'] = $this->fileUpload->upload($request->cover_image);
            }
            
            if ($request->has('hover_image') && $request->hover_image) {
                $data['hover_image'] = $this->fileUpload->upload($request->hover_image);
            }
            
            if ($request->has('size_chart') && $request->size_chart) {
                $data['size_chart'] = $this->fileUpload->upload($request->size_chart);
            }
            
            if ($request->has('og_image') && $request->og_image) {
                $data['og_image'] = $this->fileUpload->upload($request->og_image, false, null, null, 1200, 630);
            }
        
    

            return ProductResource::make(Product::create($data));
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
        //
    }
}
