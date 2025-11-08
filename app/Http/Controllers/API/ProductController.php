<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ProductRequest;
use App\Actions\FileUploadAction;
use App\Actions\MockupGenerator;
use App\Http\Resources\Store\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    protected $fileUpload;
    protected $mockupGenerator;

    public function __construct(FileUploadAction $fileUpload, MockupGenerator $mockupGenerator)
    {
        $this->fileUpload = $fileUpload;
        $this->mockupGenerator = $mockupGenerator;
    }
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
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        
        // Handle file uploads
        if ($request->has('cover_image') && $request->cover_image) {
            $data['cover_image'] = $this->fileUpload->upload($request->cover_image, false, null, null, 1200, 630);
        }
        
        if ($request->has('hover_image') && $request->hover_image) {
            $data['hover_image'] = $this->fileUpload->upload($request->hover_image, false, null, null, 1200, 630);
        }
        
        if ($request->has('size_chart') && $request->size_chart) {
            $data['size_chart'] = $this->fileUpload->upload($request->size_chart, false, null, null, 1200, 630);
        }
        
        if ($request->has('og_image') && $request->og_image) {
            $data['og_image'] = $this->fileUpload->upload($request->og_image, false, null, null, 1200, 630);
        }
        $data['vendor_id'] = $request->user()->id;
        $data['store_id'] = $request->user()->id;
        return ProductResource::make(Product::create($data));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::query()->with('colors','colors.images')->findOrFail($id);
        return ProductResource::make($product);
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        // Handle file uploads
        if ($request->has('cover_image') && $request->cover_image) {
            $oldCoverImage = $product->cover_image;
            $data['cover_image'] = $this->fileUpload->upload($request->cover_image, false, $oldCoverImage, null, 1200, 630);
        }
        
        if ($request->has('hover_image') && $request->hover_image) {
            $oldHoverImage = $product->hover_image;
            $data['hover_image'] = $this->fileUpload->upload($request->hover_image, false, $oldHoverImage, null, 1200, 630);
        }
        
        if ($request->has('size_chart') && $request->size_chart) {
            $oldSizeChart = $product->size_chart;
            $data['size_chart'] = $this->fileUpload->upload($request->size_chart, false, $oldSizeChart, null, 1200, 630);
        }
        
        if ($request->has('og_image') && $request->og_image) {
            $oldOgImage = $product->og_image;
            $data['og_image'] = $this->fileUpload->upload($request->og_image, false, $oldOgImage, null, 1200, 630);
        }
        
        $product->update($data);
        return ProductResource::make($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        if($product->orderDetails()->exists()){
            $product->update([
                'is_deleted' => true
            ]);
        }else {
            if ($product->cover_image) {
                $this->fileUpload->delete($product->cover_image);
            }
            if ($product->hover_image) {
                $this->fileUpload->delete($product->hover_image);
            }
            if ($product->og_image) {
                $this->fileUpload->delete($product->og_image);
            }
            if($product->images){
                foreach ($product->images as $image) {
                    $this->fileUpload->delete($image->image);
                }
            }
            $product->delete();
        }
        
        
        return response()->json([
            'status'=> 'success',
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
