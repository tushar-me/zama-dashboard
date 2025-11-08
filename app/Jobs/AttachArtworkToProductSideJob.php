<?php

namespace App\Jobs;

use App\Actions\FileUploadAction;
use App\Actions\MockupGenerator;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSide;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachArtworkToProductSideJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sideId;
    protected $campaignId;
    protected $artworkFile;
    protected $artworkProps;

    public function __construct($sideId, $campaignId, $artworkFile, $artworkProps)
    {
        $this->sideId = $sideId;
        $this->campaignId = $campaignId;
        $this->artworkFile = $artworkFile;
        $this->artworkProps = $artworkProps;
    }

    public function handle(FileUploadAction $fileUpload, MockupGenerator $mockupGenerator)
    {
        $side = ProductSide::query()->findOrFail($this->sideId);
        $colors = ProductColor::where('product_id', $side->product_id)->get();
        $artworkPath = $this->artworkFile ?? $side->getRawOriginal('artwork');
        if($this->artworkProps){
            $side->update([
                'artwork_props' => $this->artworkProps,
            ]);
    
            $canvasSize = 1200;
            $boundingBox = $side->bounding_box;
            $actualBoundingBox = [
                'left'   => $boundingBox['left'] * $canvasSize,
                'top'    => $boundingBox['top'] * $canvasSize,
                'width'  => $boundingBox['width'] * $canvasSize,
                'height' => $boundingBox['height'] * $canvasSize,
            ];
    
            $artworkPropsCalculated = [
                'left'   =>  $actualBoundingBox['left'] + $this->artworkProps['left'] * $actualBoundingBox['width'],
                'top'    =>  $actualBoundingBox['top'] + $this->artworkProps['top'] * $actualBoundingBox['height'],
                'width'  =>  $this->artworkProps['width'] * $actualBoundingBox['width'],
                'height' =>  $this->artworkProps['height'] * $actualBoundingBox['height'],
            ];
    
            $oldImages = ProductImage::where('product_side_id', $side->id)->get();
            foreach ($oldImages as $oldImage) {
                $fileUpload->delete($oldImage->image);
            }
            ProductImage::where('product_side_id', $side->id)->delete();
    
       
            foreach($colors as $color){
                $generatedImage = $mockupGenerator->generateSingle(
                    $side->getRawOriginal('image'), 
                    $artworkPath,
                    $artworkPropsCalculated,
                    $color->hex_code
                );
    
                ProductImage::create([
                    'product_id' => $side->product_id,
                    'campaign_id' => $this->campaignId,
                    'product_side_id' => $side->id,
                    'product_color_id' => $color->id,
                    'mockup' => $side->getRawOriginal('image'),
                    'side' => $side->name,
                    'color_code' => $color->hex_code,
                    'image' => $generatedImage,
                ]);
            }
        }else{
            $boundingBox = $side->bounding_box;
                $canvasSize = 1200;
                $actualBoundingBox = [
                    'left'   => $boundingBox['left'] * $canvasSize,
                    'top'    => $boundingBox['top'] * $canvasSize,
                    'width'  => $boundingBox['width'] * $canvasSize,
                    'height' => $boundingBox['height'] * $canvasSize,
                ];
                $oldImages = ProductImage::where('product_side_id', $side->id)->get();
                foreach ($oldImages as $oldImage) {
                    $fileUpload->delete($oldImage->image);
                }
                ProductImage::where('product_side_id', $side->id)->delete();
                foreach($colors as $color){
                    $result = $mockupGenerator->generateOnce(
                        $side->getRawOriginal('image'),    
                        $artworkPath,    
                        $actualBoundingBox,      
                        $color->hex_code    
                    );
                    $side->update([
                        'artwork' => $artworkPath,
                        'artwork_props' => $result['artwork_props'],
                    ]);
                    ProductImage::create([
                        'product_id' => $side->product_id,
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
