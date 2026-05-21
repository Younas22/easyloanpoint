<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::allKeyed();
        return view('home', compact('settings'));
    }

    public function downloadApk()
    {
        $apkPath = Setting::get('apk_file');

        if (! $apkPath || ! file_exists(public_path($apkPath))) {
            return redirect()->route('home')
                ->with('error', 'APK is not available yet. Please check back soon.');
        }

        return response()->download(public_path($apkPath), 'easyloanpoint.apk', [
            'Content-Type'        => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="easyloanpoint.apk"',
        ]);
    }
}
