<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

/**
 * Semua upload gambar dikecilkan di browser sebelum dikirim ke server,
 * supaya hemat disk & bandwidth STB.
 */
class Uploads
{
    public static function image(string $field, string $directory, int $width = 1600): FileUpload
    {
        return FileUpload::make($field)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->maxSize(8192)
            ->automaticallyResizeImagesMode('contain')
            ->automaticallyResizeImagesToWidth((string) $width)
            ->automaticallyResizeImagesToHeight((string) $width)
            ->imageEditor();
    }

    public static function gallery(string $field, string $directory, int $max = 30): FileUpload
    {
        return static::image($field, $directory)
            ->multiple()
            ->reorderable()
            ->appendFiles()
            ->maxFiles($max)
            ->panelLayout('grid')
            ->imageEditor(false);
    }
}
