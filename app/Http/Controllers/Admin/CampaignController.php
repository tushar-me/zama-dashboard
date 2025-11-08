<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CampaignRequest;
use App\Models\Campaign;
use App\Actions\FileUploadAction;
use Illuminate\Http\Request;

class CampaignController extends Controller
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
        $campaigns = Campaign::with('store:id,name')->latest()->paginate(20);
       return inertia('Vendor/Campaign/Index', [
        'campaigns' => $campaigns,
       ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignRequest $request)
    {
        $data = $request->validated();
        
        // Handle image upload
        if ($request->has('image') && $request->image) {
            $data['image'] = $this->fileUpload->upload($request->image, false, null, null, 1200, 630);
        }
        
        // Handle OG image upload
        if ($request->has('og_image') && $request->og_image) {
            $data['og_image'] = $this->fileUpload->upload($request->og_image, false, null, null, 1200, 630);
        }
        
        $campaign = Campaign::create($data);
        
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $campaign = Campaign::with('store')->findOrFail($id);
        return view('admin.campaigns.show', compact('campaign'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignRequest $request, string $id)
    {
        $campaign = Campaign::findOrFail($id);
        $data = $request->validated();
        
        // Handle image upload
        if ($request->has('image') && $request->image) {
            $oldImage = $campaign->image;
            $data['image'] = $this->fileUpload->upload($request->image, false, $oldImage, null, 1200, 630);
        }
        
        // Handle OG image upload
        if ($request->has('og_image') && $request->og_image) {
            $oldOgImage = $campaign->og_image;
            $data['og_image'] = $this->fileUpload->upload($request->og_image, false, $oldOgImage, null, 1200, 630);
        }
        
        $campaign->update($data);
        
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        
        // Delete images
        if ($campaign->image) {
            $this->fileUpload->delete($campaign->image);
        }
        
        if ($campaign->og_image) {
            $this->fileUpload->delete($campaign->og_image);
        }
        
        $campaign->delete();
        
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}
