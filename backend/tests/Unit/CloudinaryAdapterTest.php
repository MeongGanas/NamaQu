<?php

namespace Tests\Unit;

use App\Filesystem\CloudinaryAdapter;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Config;
use Tests\TestCase;

/** Tanpa koneksi ke Cloudinary: hanya pemetaan path -> public_id/URL & registrasi disk. */
class CloudinaryAdapterTest extends TestCase
{
    private function adapter(string $folder = 'namaqu'): CloudinaryAdapter
    {
        return new CloudinaryAdapter(new Cloudinary('cloudinary://123:secret@demo-cloud'), $folder);
    }

    public function test_path_maps_to_public_id_without_extension(): void
    {
        $this->assertSame('namaqu/activities/photos/abc', $this->adapter()->publicId('activities/photos/abc.jpg'));
        $this->assertSame('namaqu/testimonials/zidan', $this->adapter()->publicId('/testimonials/zidan.jpg'));
        $this->assertSame('team/x', $this->adapter('')->publicId('team/x.webp'));
    }

    public function test_url_points_to_cloudinary_cdn_with_auto_format(): void
    {
        $this->assertSame(
            'https://res.cloudinary.com/demo-cloud/image/upload/f_auto,q_auto/namaqu/activities/photos/abc',
            $this->adapter()->getUrl('activities/photos/abc.jpg'),
        );
    }

    public function test_cloudinary_disk_is_registered_and_used_as_media_disk(): void
    {
        config([
            'filesystems.disks.cloudinary.cloudinary_url' => 'cloudinary://123:secret@demo-cloud',
            'filesystems.media' => 'cloudinary',
        ]);

        $this->assertSame(
            'https://res.cloudinary.com/demo-cloud/image/upload/f_auto,q_auto/namaqu/team/nadia',
            Storage::disk(config('filesystems.media'))->url('team/nadia.jpg'),
        );
        $this->assertSame('public', Storage::disk('cloudinary')->getVisibility('team/nadia.jpg'));
        $this->assertSame('image/jpeg', Storage::disk('cloudinary')->mimeType('team/nadia.jpg'));
    }

    public function test_directory_operations_are_noops(): void
    {
        $adapter = $this->adapter();
        $adapter->createDirectory('activities', new Config);
        $this->assertTrue($adapter->directoryExists('activities'));
        $this->assertSame([], $adapter->listContents('activities', true));
    }
}
