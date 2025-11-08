<?php
namespace App\Jobs;

use App\Actions\MockupGenerator;
use App\Models\ProductImage;
use App\Models\ProductSide;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Type\TrueType;

class GenerateMockupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $sideId;
    protected string $colorId;
    protected string $productId;
    protected string $campaignId;
    protected string $artworkPath;

    public function __construct(string $sideId, string $colorId, string $productId, string $campaignId, string $artworkPath)
    {
        $this->sideId = $sideId;
        $this->colorId = $colorId;
        $this->productId = $productId;
        $this->campaignId = $campaignId;
        $this->artworkPath = $artworkPath;
    }

    public function handle(MockupGenerator $mockupGenerator): void
    {
        $side = ProductSide::findOrFail($this->sideId);
        $boundingBox = $side->bounding_box;
        $artworkProps = $side->artwork_props;

        $canvasSize = 1200;
        $actualBoundingBox = [
            'left' => $boundingBox['left'] * $canvasSize,
            'top' => $boundingBox['top'] * $canvasSize,
            'width' => $boundingBox['width'] * $canvasSize,
            'height' => $boundingBox['height'] * $canvasSize,
        ];
        $actualArtworkProps = [
            'left'   =>  $actualBoundingBox['left'] + $artworkProps['left'] * $actualBoundingBox['width'],
            'top'    => $actualBoundingBox['top'] + $artworkProps['top'] * $actualBoundingBox['height'],
            'width'  => $artworkProps['width'] * $actualBoundingBox['width'],
            'height' => $artworkProps['height'] * $actualBoundingBox['height'],
        ];

        $generatedImage = $mockupGenerator->generateSingle(
            $side->getRawOriginal('image'),
            $this->artworkPath,
            $actualArtworkProps,
            $side->product->colors()->find($this->colorId)->hex_code
        );

        ProductImage::create([
            'product_id' => $this->productId,
            'campaign_id' => $this->campaignId,
            'product_side_id' => $this->sideId,
            'product_color_id' => $this->colorId,
            'mockup' => $side->getRawOriginal('image'),
            'side' => $side->name,
            'color_code' => $side->product->colors()->find($this->colorId)->hex_code,
            'image' => $generatedImage,
        ]);
    }
}
