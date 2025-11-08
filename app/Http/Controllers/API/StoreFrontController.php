<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreFrontRequest;
use App\Models\StoreFront;
use App\Actions\FileUploadAction;
use App\Http\Resources\Store\V1\StoreFrontResource;
use Illuminate\Http\Request;

class StoreFrontController extends Controller
{
    protected $fileUpload;

    public function __construct(FileUploadAction $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storeFronts = StoreFront::latest()->search(['name','status','slug'],request()->search);
        return StoreFrontResource::collection($storeFronts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFrontRequest $request)
    {
        $data = $request->validated();
        
        // Handle banner upload
        if ($request->has('banner') && $request->banner) {
            $data['banner'] = $this->fileUpload->upload($request->banner, false, null, null, 1200, 630);
        }
        
        // Handle OG image upload
        if ($request->has('og_image') && $request->og_image) {
            $data['og_image'] = $this->fileUpload->upload($request->og_image, false, null, null, 1200, 630);
        }
        
        $storeFront = StoreFront::create($data);

        $storeFront->campaigns()->attach($request->campaign_ids);
        
        return response()->json($storeFront, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $storeFront = StoreFront::findOrFail($id);
        return response()->json([
            'data' => $storeFront,
            'message' => 'Store front fetched successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreFrontRequest $request, string $id)
    {
        $storeFront = StoreFront::findOrFail($id);
        $data = $request->validated();
        
        // Handle banner upload
        if ($request->has('banner') && $request->banner) {
            $oldBanner = $storeFront->banner;
            $data['banner'] = $this->fileUpload->upload($request->banner, false, $oldBanner, null, 1200, 630);
        }
        
        // Handle OG image upload
        if ($request->has('og_image') && $request->og_image) {
            $oldOgImage = $storeFront->og_image;
            $data['og_image'] = $this->fileUpload->upload($request->og_image, false, $oldOgImage, null, 1200, 630);
        }
        
        $storeFront->update($data);
        $storeFront->campaigns()->attach($request->campaign_ids);
        
        return response()->json($storeFront);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $storeFront = StoreFront::findOrFail($id);
        
        // Delete images
        if ($storeFront->banner) {
            $this->fileUpload->delete($storeFront->banner);
        }
        
        if ($storeFront->og_image) {
            $this->fileUpload->delete($storeFront->og_image);
        }
        
        $storeFront->delete();
        
        return response()->json(['message' => 'Store front deleted successfully']);
    }
}
