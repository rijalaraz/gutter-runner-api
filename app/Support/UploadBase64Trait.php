<?php

namespace App\Support;

use FileEye\MimeMap\Type;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Illuminate\Support\Str;

/**
 * Class UploadBase64Trait.
 */
trait UploadBase64Trait
{
    public function uploadFile($pieces_jointe, $dir = null): string {

        $base64 = $pieces_jointe['url'];

        $fileData = base64_decode($base64);

        $detector = new FinfoMimeTypeDetector();

        $mimeType = $detector->detectMimeTypeFromBuffer($fileData);

        $type = new Type($mimeType);

        $extension = $type->getDefaultExtension();

        // $public = storage_path('app/public');
        $uploads = config('upload.folder');

        $name = Str::slug(pathinfo($pieces_jointe['file_name'], PATHINFO_FILENAME));

        if($dir) {
            $subDir = $extension;
            $subDir .= '/'.rand(0, 9);
            $dir .= '/'.$subDir;
            $fileName = $uploads.'/'.$dir.'/'.$name.'.'.$extension;
        } else {
            $fileName = $uploads.'/'.$name.'.'.$extension;
        }

        // $filePath = $public.'/'.$fileName;

        Storage::disk('public')->put($fileName, $fileData);

        // $this->fileSizeExceedsLimit($filePath);

        return $fileName;
    }

    private function fileSizeExceedsLimit($filepath) {
        $uploadMaxFileSize = config('upload.max_file_size');

        if(file_exists($filepath)) {
            $filesize = filesize($filepath);
            if($filesize > $uploadMaxFileSize) {
                unlink($filepath);
                return response()->json(['file' => trans('upload.max', [
                    'max_size' => $this->formatBytes($uploadMaxFileSize,0)
                ])], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    private function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        $bytes /= pow(1024, $pow); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}
