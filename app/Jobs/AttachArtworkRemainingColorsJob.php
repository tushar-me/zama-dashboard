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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttachArtworkRemainingColorsJob implements ShouldQueue
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
        $side = ProductSide::findOrFail($this->sideId);
        Log::info('colors', [$side]);
        $colors = ProductColor::where('product_id', $side->product_id)
            ->where('is_default', false)
            ->get();
        $artworkPath = $this->artworkFile ?? $side->getRawOriginal('artwork');

        $boundingBox = $side->bounding_box;
        $canvasSize = 1200;
        $actualBoundingBox = [
            'left' => $boundingBox['left'] * $canvasSize,
            'top' => $boundingBox['top'] * $canvasSize,
            'width' => $boundingBox['width'] * $canvasSize,
            'height' => $boundingBox['height'] * $canvasSize,
        ];
       
        if($this->artworkProps){
            $side->update([
                'artwork_props' => $this->artworkProps,
            ]);
            $artworkPropsCalculated = [
                'left'   =>  $actualBoundingBox['left'] + $this->artworkProps['left'] * $actualBoundingBox['width'],
                'top'    =>  $actualBoundingBox['top'] + $this->artworkProps['top'] * $actualBoundingBox['height'],
                'width'  =>  $this->artworkProps['width'] * $actualBoundingBox['width'],
                'height' =>  $this->artworkProps['height'] * $actualBoundingBox['height'],
            ];
            foreach ($colors as $color) {
                $oldImages = ProductImage::where('product_side_id', $side->id)->where('product_color_id', $color->id)->get();
                foreach ($oldImages as $oldImage) {
                    $fileUpload->delete($oldImage->image);
                }
                ProductImage::where('product_side_id', $side->id)->where('product_color_id', $color->id)->delete();
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
            foreach ($colors as $color) {
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
