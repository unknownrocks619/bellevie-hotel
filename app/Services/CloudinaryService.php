<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private Cloudinary $cloudinary;
    private string $uploadFolder;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ]
        ]);
        $this->uploadFolder = config('cloudinary.upload_folder', 'bellevie_hotel');
    }

    public function upload(UploadedFile $file, string $subfolder = '', array $options = []): array
    {
        $folder = $this->uploadFolder;
        if ($subfolder) {
            $folder .= '/' . $subfolder;
        }

        $defaultOptions = [
            'folder' => $folder,
            'resource_type' => 'auto',
        ];

        $uploadOptions = array_merge($defaultOptions, $options);

        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $uploadOptions
            );

            return [
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Upload failed: ' . $e->getMessage());
        }
    }

    public function delete(string $publicId): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function resizeUrl(string $url, int $width, int $height): string
    {
        return $this->cloudinary->image($url)->resize(
            \Cloudinary\Transformation\Resize::fill($width, $height)
        )->toUrl();
    }
}
