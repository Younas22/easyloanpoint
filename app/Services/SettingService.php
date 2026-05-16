<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class SettingService
{
    public function upload(UploadedFile $file, string $subDir): string
    {
        $dir = public_path($subDir);

        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return $subDir . '/' . $filename;
    }

    public function deleteFile(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    public function saveMany(array $data, string $group): void
    {
        foreach ($data as $key => $value) {
            Setting::set($key, $value, $group);
        }
    }
}
