<?php

namespace App\Http\Support\Manipulation;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class ImageHelper
{
    protected array $imageExtensions = [
        'jpg',
        'gif',
        'png',
        'jpeg',
    ];

    public static function uploadImage(mixed $images, ?Model $model = null, array $additionalRequest = []): bool|array
    {
        $settings = config('image-settings');
        $records = [];
        if (!is_array($images)) {
            $images = [$images];
        }

        foreach ($images as $image) {

            $generatedFilename = $image->hashName();

            list($width, $height) = getimagesize($image->getRealPath());

            foreach ($settings['sizes'] as $folder => $option) {
                $resized = Image::read($image->getRealPath())
                    ->scaleDown($option['width'], $option['height'])
                    ->encode();

                $baseDir = 'uploads/' . $folder . '/' . date("Y") . '/' . date('m');
                Storage::put($baseDir . '/' . $generatedFilename, (string) $resized);
            }

            $image->store('uploads/org/' . date('Y') . '/' . date('m'));
            $exif = Image::read($image->getRealPath())->exif();
            $newImage = new ModelsImage();
            $newImage->fill([
                'filename' => $generatedFilename,
                'filepath' => date('Y') . '/' . date("m") . '/' . $generatedFilename,
                'information' => [
                    'exif' => $exif,
                    'folders' => date('Y') . '/' . date("m")
                ],
                'sizes' => [
                    'width' => $width ?? 0,
                    'height' => $height ?? 0,
                ],
                'original_filename' => $image->getClientOriginalName(),
            ]);

            if (!$newImage->save()) {
                return false;
            }

            if (!is_null($model)) {
                $fileRelation = new FileRelation();
                $fileRelation->fill([
                    'image_id' => $newImage->getKey(),
                    'relation' => $model->getTable(),
                    'relation_id' => $model->getKey()
                ]);

                if (count($model::$image_type) == 1) {
                    foreach ($model::$image_type as $image_key => $_) {
                        $fileRelation->type = $image_key;
                    }
                }

                if (!$fileRelation->save()) {
                    return false;
                }
            }

            $records[] = ['image' => $newImage, 'relation' => $fileRelation];
        }

        return $records;
    }
}
