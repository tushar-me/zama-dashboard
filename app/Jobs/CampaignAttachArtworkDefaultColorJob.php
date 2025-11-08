<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampaignAttachArtworkDefaultColorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;
    protected $artworkPath;

    public function __construct($campaignId, $artworkPath)
    {
        $this->campaignId = $campaignId;
        $this->artworkPath = $artworkPath;
    }

    public function handle()
    {
        $campaign = Campaign::query()->findOrFail($this->campaignId);  
        $campaign->update([
            'image' =>  $this->artworkPath
        ]);

        $products = $campaign->products()->with('productSides','colors')->get();

        foreach($products as $product){
            foreach($product->productSides as $side){
                $side->update(['artwork' =>  $this->artworkPath]);
                $boundingBox = $side->bounding_box;
                $canvasSize = 1200;
                $actualBoundingBox = [
                    'left'   => $boundingBox['left'] * $canvasSize,
                    'top'    => $boundingBox['top'] * $canvasSize,
                    'width'  => $boundingBox['width'] * $canvasSize,
                    'height' => $boundingBox['height'] * $canvasSize,
                ];
                $color = ProductColor::query()->where('product_id', $product->id)->where('is_default', true)->fisrt();
                $result = app('App\Actions\MockupGenerator')->generateOnce(
                    $side->getRawOriginal('image'),    
                    $this->artworkPath,    
                    $actualBoundingBox,      
                    $color->hex_code    
                );
                $side->update([
                    'artwork_props' => $result['artwork_props'],
                ]);
                ProductImage::create([
                    'product_id' => $product->id,
                    'campaign_id' => $this->campaignId,
                    'product_side_id' => $side->id,
                    'product_color_id' => $color->id,
                    'mockup' => $side->getRawOriginal('image'),
                    'side' => $side->name,
                    'color_code' => $color->hex_code,
                    'image' => $result['file'],
                ]);
            }
        }
    }
}
