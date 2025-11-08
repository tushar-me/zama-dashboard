<?php

namespace App\Actions;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Actions\FileUploadAction;
use Illuminate\Support\Facades\Storage;
class MockupGenerator
{
    protected $manager;
    protected $fileAction;
    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        $this->fileAction = new FileUploadAction();
    }
    public function generateOnce(string $baseImagePath, string $artworkPath, array $boundingBox, string $bgColor = '#ffffff')
    {
        if (!Storage::disk('public')->exists($baseImagePath)) {
            throw new \Exception("Mockup not found: $baseImagePath");
        }
        if (!Storage::disk('public')->exists($artworkPath)) {
            throw new \Exception("Artwork not found: $artworkPath");
        }
        $canvas = $this->manager->create(1200, 1200)->fill($bgColor);
        $artwork = $this->manager->read(Storage::disk('public')->path($artworkPath));
        $artwork->scale($boundingBox['width']);
        if ($artwork->height() > $boundingBox['height']) {
            $artwork->scale( $boundingBox['height']);
        }
        $offsetX = $boundingBox['left'] + intval(($boundingBox['width'] - $artwork->width()) / 2);
        $offsetY = $boundingBox['top'] + intval(($boundingBox['height'] - $artwork->height()) / 2);
        $canvas->place($artwork, 'top-left', $offsetX, $offsetY);
        $mockup = $this->manager->read(Storage::disk('public')->path($baseImagePath));
        $mockup->resize(1200, 1200);
        $canvas->place($mockup, 'top-left', 0, 0);
        $imageData = $canvas->toWebp(90);
        $fileName = storeCode().'/mockups/'.uniqid().'.webp';
        Storage::disk('public')->put($fileName, $imageData, ['visibility' => 'public']);
        $artworkProps = [
            'left'   => ($offsetX - $boundingBox['left']) / $boundingBox['width'],
            'top'    => ($offsetY - $boundingBox['top']) / $boundingBox['height'],
            'width'  => $artwork->width() / $boundingBox['width'],
            'height' => $artwork->height() / $boundingBox['height'],
        ];
    
        return [
            'file' => $fileName,
            'artwork_props' => $artworkProps,
        ];
    }

    public function generateSingle(string $mockupPath, string $artworkPath, array $artworkProps, string $bgColor = '#ffffff')
    {
        $canvasSize = 1200;
        $canvas = $this->manager->create($canvasSize, $canvasSize)->fill($bgColor);
        $artwork = $this->manager->read(Storage::disk('public')->path($artworkPath));
        $artwork->resize($artworkProps['width'], $artworkProps['height']);
        $canvas->place($artwork, 'top-left', $artworkProps['left'], $artworkProps['top']);
        $mockup = $this->manager->read(Storage::disk('public')->path($mockupPath));
        $mockup->resize($canvasSize, $canvasSize);
        $canvas->place($mockup, 'top-left', 0, 0);
        $fileName = storeCode().'/mockups/'.uniqid().'.webp';
        $imageData = $canvas->toWebp(90);
        Storage::disk('public')->put($fileName, $imageData, ['visibility' => 'public']);
        Log::info('call sing'.$fileName);
        return $fileName;
    }
    public function generateColorImage(string $mockupPath, string $bgColor = '#ffffff')
    {
        $canvasSize = 1200;
        $canvas = $this->manager->create($canvasSize, $canvasSize)->fill($bgColor);
        $mockup = $this->manager->read(Storage::disk('public')->path($mockupPath));
        $mockup->resize($canvasSize, $canvasSize);
        $canvas->place($mockup, 'top-left', 0, 0);
        $fileName = storeCode().'/mockups/'.uniqid().'.webp';
        $imageData = $canvas->toWebp(90);
        Storage::disk('public')->put($fileName, $imageData, ['visibility' => 'public']);

        return $fileName;
    }
}
