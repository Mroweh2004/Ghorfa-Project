<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyImageCompressor
{
    private const JPEG_QUALITY = 82;

    private const WEBP_QUALITY = 82;

    private const PNG_COMPRESSION = 6;

    public static function store(UploadedFile $file, string $directory = 'property-images', string $disk = 'public'): string
    {
        if (!extension_loaded('gd')) {
            return $file->store($directory, $disk);
        }

        $compressed = self::compressToBinary($file);
        if ($compressed === null) {
            return $file->store($directory, $disk);
        }

        $extension = self::outputExtension($file);
        $path = $directory.'/'.Str::uuid().'.'.$extension;
        Storage::disk($disk)->put($path, $compressed);

        return $path;
    }

    private static function compressToBinary(UploadedFile $file): ?string
    {
        $mime = strtolower((string) $file->getMimeType());

        if ($mime === 'image/gif') {
            return null;
        }

        $source = self::createImageResource($file, $mime);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);

            return null;
        }

        $canvas = imagecreatetruecolor($width, $height);
        if ($canvas === false) {
            imagedestroy($source);

            return null;
        }

        if (in_array($mime, ['image/png', 'image/webp'], true)) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $width, $height);
        imagedestroy($source);

        ob_start();
        $written = match ($mime) {
            'image/png' => imagepng($canvas, null, self::PNG_COMPRESSION),
            'image/webp' => function_exists('imagewebp')
                ? imagewebp($canvas, null, self::WEBP_QUALITY)
                : imagejpeg($canvas, null, self::JPEG_QUALITY),
            default => imagejpeg($canvas, null, self::JPEG_QUALITY),
        };
        $binary = ob_get_clean();
        imagedestroy($canvas);

        if (!$written || !is_string($binary) || $binary === '') {
            return null;
        }

        if (strlen($binary) >= $file->getSize()) {
            return null;
        }

        return $binary;
    }

    /**
     * @return \GdImage|false
     */
    private static function createImageResource(UploadedFile $file, string $mime): \GdImage|false
    {
        $path = $file->getRealPath();
        if ($path === false) {
            return false;
        }

        return match ($mime) {
            'image/png' => imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            'image/gif' => imagecreatefromgif($path),
            default => imagecreatefromjpeg($path),
        };
    }

    private static function outputExtension(UploadedFile $file): string
    {
        $mime = strtolower((string) $file->getMimeType());

        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => function_exists('imagewebp') ? 'webp' : 'jpg',
            default => 'jpg',
        };
    }
}
