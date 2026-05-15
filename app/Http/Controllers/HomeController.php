<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function downloadApk()
    {
        $path = public_path('downloads/app-release.apk');

        if (! file_exists($path)) {
            return redirect()->route('home')
                ->with('error', 'APK is not available yet. Please check back soon.');
        }

        return response()->download($path, 'EasyLoanPoint.apk', [
            'Content-Type'        => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="EasyLoanPoint.apk"',
        ]);
    }
}
