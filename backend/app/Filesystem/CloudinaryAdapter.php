<?php

namespace App\Filesystem;

use Cloudinary\Api\Exception\NotFound;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Http;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use Throwable;

/**
 * Disk Laravel/Flysystem yang menyimpan gambar di Cloudinary.
 *
 * Path di database tetap seperti disk lokal (mis. "activities/photos/abc.jpg");
 * di Cloudinary disimpan sebagai public_id "<folder>/activities/photos/abc".
 * Gambar disajikan langsung dari CDN Cloudinary, tidak lewat server rumah.
 */
class CloudinaryAdapter implements FilesystemAdapter
{
    private ExtensionMimeTypeDetector $mime;

    public function __construct(
        private readonly Cloudinary $cloudinary,
        private readonly string $folder = 'namaqu',
    ) {
        $this->mime = new ExtensionMimeTypeDetector;
    }

    /** "activities/photos/abc.jpg" -> "namaqu/activities/photos/abc" */
    public function publicId(string $path): string
    {
        $path = ltrim($path, '/');
        $withoutExtension = preg_replace('/\.[^.\/]+$/', '', $path);

        return trim($this->folder, '/') !== ''
            ? trim($this->folder, '/').'/'.$withoutExtension
            : $withoutExtension;
    }

    /** Dipanggil Storage::disk(...)->url($path). */
    public function getUrl(string $path): string
    {
        return sprintf(
            'https://res.cloudinary.com/%s/image/upload/f_auto,q_auto/%s',
            $this->cloudinary->configuration->cloud->cloudName,
            $this->publicId($path),
        );
    }

    private function originalUrl(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        return sprintf(
            'https://res.cloudinary.com/%s/image/upload/%s.%s',
            $this->cloudinary->configuration->cloud->cloudName,
            $this->publicId($path),
            $extension,
        );
    }

    public function fileExists(string $path): bool
    {
        try {
            $this->cloudinary->adminApi()->asset($this->publicId($path));

            return true;
        } catch (NotFound) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        return true; // Cloudinary tidak punya konsep folder yang perlu dibuat
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'cld');
        file_put_contents($tmp, $contents);

        try {
            $this->upload($path, $tmp);
        } finally {
            @unlink($tmp);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'cld');
        $out = fopen($tmp, 'wb');
        stream_copy_to_stream($contents, $out);
        fclose($out);

        try {
            $this->upload($path, $tmp);
        } finally {
            @unlink($tmp);
        }
    }

    private function upload(string $path, string $file): void
    {
        try {
            $this->cloudinary->uploadApi()->upload($file, [
                'public_id' => $this->publicId($path),
                'resource_type' => 'image',
                'overwrite' => true,
                'invalidate' => true,
                'use_filename' => false,
                'unique_filename' => false,
            ]);
        } catch (Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function read(string $path): string
    {
        $response = Http::timeout(30)->get($this->originalUrl($path));

        if (! $response->successful()) {
            throw UnableToReadFile::fromLocation($path, 'HTTP '.$response->status());
        }

        return $response->body();
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $this->read($path));
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $this->cloudinary->uploadApi()->destroy($this->publicId($path), ['invalidate' => true]);
    }

    public function deleteDirectory(string $path): void
    {
        // tidak dipakai aplikasi
    }

    public function createDirectory(string $path, Config $config): void
    {
        // folder Cloudinary dibuat otomatis saat upload
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // semua gambar publik
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, Visibility::PUBLIC);
    }

    public function mimeType(string $path): FileAttributes
    {
        $mime = $this->mime->detectMimeTypeFromPath($path);

        if (! $mime) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return new FileAttributes($path, null, null, null, $mime);
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, strtotime($this->asset($path)['created_at'] ?? 'now'));
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path, (int) ($this->asset($path)['bytes'] ?? 0));
    }

    private function asset(string $path): array
    {
        try {
            return $this->cloudinary->adminApi()->asset($this->publicId($path))->getArrayCopy();
        } catch (Throwable $e) {
            throw UnableToRetrieveMetadata::create($path, 'metadata', $e->getMessage(), $e);
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return []; // tidak dipakai aplikasi
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->cloudinary->uploadApi()->rename($this->publicId($source), $this->publicId($destination), ['overwrite' => true, 'invalidate' => true]);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->cloudinary->uploadApi()->upload($this->originalUrl($source), [
            'public_id' => $this->publicId($destination),
            'resource_type' => 'image',
            'overwrite' => true,
        ]);
    }
}
