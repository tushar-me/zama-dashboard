<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Actions\FileUploadAction;
use App\Actions\MockupGenerator;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Size;

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
    public function index()
    {
        $products = Product::query()->with('category:id,name', 'creator:id,name', 'editor:id,name')->search(['name','code'], request()->search);
        return inertia('Product/Index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Product/Create',[
            'categories' => Category::query()->select('id', 'name', 'image')->get(),
            'sizes' => Size::query()->orderBy('order_level')->select(['id','name','order_level'])->get(),
        ]);
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
            
            if ($request->has('measurement_guide') && $request->measurement_guide) {
                $data['measurement_guide'] = $this->fileUpload->upload($request->measurement_guide);
            }
            
            if ($request->has('og_image') && $request->og_image) {
                $data['og_image'] = $this->fileUpload->upload($request->og_image, false, null, null, 1200, 630);
            }
            $product = Product::create($data);
            if($request->size_chart){
                $product->sizeChart()->create([
                    'title' => 'Size Chart',
                    'chart_data' => $request->size_chart
                ]);
            }

            if($request->variations){
                foreach($request->variations as $variation){
                    $product->variations()->create([
                        'size_id' => $variation['id'],
                        'sku' => rand(99999999,10000000),
                        'compare_price' => $variation['compare_price'],
                        'price' => $variation['price'],
                    ]);
                }
            }
            return to_route('product.index');
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
        
        
        return redirect()->back();
    }
}
