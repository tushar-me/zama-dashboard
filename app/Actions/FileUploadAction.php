<?php 
namespace App\Actions;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Decoders\DataUriImageDecoder;
use Intervention\Image\Decoders\Base64ImageDecoder;

class FileUploadAction {
    protected $manager;
    public function __construct() {
        $this->manager = new ImageManager(new Driver());
    }
    public function upload($file, $raw = false, $existingFile = null,$width = 1200, $height = 1200, $disk = null, ) {
        $disk = $disk ?? config('filesystems.default');
        $folder = null;
        if (auth()->guard('admin')->check()) {
            $folder = 'assets';
        } elseif (request()->user()) {
            $folder = storeCode();
        } else {
            $folder = 'files';
        }
        if ($raw) {
            $extension = $file->extension();
            $name = Str::uuid() . '.' . $extension;
            $file = $file->storeAs($folder, $name, $disk);
        } else {
            $content = file_get_contents($file->getRealPath());
            $dataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode($content);
            $image = $this->manager->read($dataUri, [
                    DataUriImageDecoder::class,
                    Base64ImageDecoder::class,
                ]);
            if($width && $height){
                $image->resize($width, $height);
            }
            $imageData = $image->toWebp(60);
            $file = $folder . '/' . Str::uuid() . '.webp';
            Storage::disk($disk)->put($file, $imageData, ['visibility' => 'public']);
        }
        if ($existingFile) {
            Storage::disk($disk)->delete($existingFile);
        }
        return $file;
    }
    public function delete($file, $disk = null) {
        $disk = $disk ?? config('filesystems.default');
        $relativePath = str_replace('/storage/', '', parse_url($file, PHP_URL_PATH));
        if (Storage::disk($disk)->exists($relativePath)) {
            Storage::disk($disk)->delete($relativePath);
            return true;
        }
        logger("File not found: $relativePath on disk $disk");
        return false;
    }
}