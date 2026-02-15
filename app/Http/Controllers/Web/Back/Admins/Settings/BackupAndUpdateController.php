<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class BackupAndUpdateController extends Controller
{
    public function take(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        // not working in local
        Artisan::call('backup:run');

        return redirect()->back()->with('success', __('l.Backup created successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $name = $request->backup;
        $path = public_path('backup/laravel/' . $name);

        if (File::exists($path)) {
            File::delete($path);
            return redirect()->back()->with('success', __('l.Backup deleted successfully'));
        }

        return redirect()->back()->with('error', __('l.Backup file not found'));
    }

    public function checkUpdate()
    {

    }

    public function runUpdate()
    {

    }
}
