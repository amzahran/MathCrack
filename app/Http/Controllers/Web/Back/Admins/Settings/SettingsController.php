<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Models\Language;
use App\Models\BlockedIp;

class SettingsController extends Controller
{
    public function index()
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $themes = Theme::all();
        $languages = Language::where('is_active', true)->get();
        $ips = BlockedIp::orderByDesc('id')->get();

        $backupPath = public_path('backup/laravel');
        $backups = [];

        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => number_format($file->getSize() / 1048576, 2), // تحويل الحجم إلى ميجابايت
                    'date' => date('Y-m-d H:i:s', $file->getMTime())
                ];
            }
        }

        return view('themes/default/back.admins.settings.index', ['themes' => $themes, 'languages' => $languages, 'ips' => $ips, 'backups' => $backups]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }
        // return $request->all();

        // update logo if exist
        if ($request->hasFile('logo')) {
            $logoo = Setting::where('option', 'logo')->first();
            $logo = $logoo->value;
            if ($logo != null) {
                $path = public_path($logo);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $image = $request->file('logo');
            $filename = 'logo.' . $image->getClientOriginalExtension();
            $image->move(public_path('/'), $filename);
            $logoo->update(['value' => $filename]);
        }

        // update Black logo if exist
        if ($request->hasFile('logo_black')) {
            $logooBlack = Setting::where('option', 'logo_black')->first();
            $logoBlack = $logooBlack->value;
            if ($logoBlack != null) {
                $path = public_path($logoBlack);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $image = $request->file('logo_black');
            $filename = 'logo-black.' . $image->getClientOriginalExtension();
            $image->move(public_path('/'), $filename);
            $logooBlack->update(['value' => $filename]);
        }

        // update favicon if exist
        if ($request->hasFile('favicon')) {
            $faviconn = Setting::where('option', 'favicon')->first();
            $favicon = $faviconn->value;
            if ($favicon != null) {
                $path = public_path($favicon);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $image = $request->file('favicon');
            $filename = 'favicon.' . $image->getClientOriginalExtension();
            $image->move(public_path('/'), $filename);
            $faviconn->update(['value' => $filename]);
        }

        // update theme
        if ($request->hasFile('new_theme')) {
            $this->handleThemeUpload($request->new_theme);
        }

        $data = [];

        // update general settings in env file if exist
        if ($request->filled('name', 'domain', 'default_language', 'timezone', 'maintenance')) {
            $data['APP_NAME'] = $request->name;
            $data['APP_URL'] = $request->domain;
            $data['APP_TIMEZONE'] = $request->timezone;
            $data['APP_LOCALE'] = $request->default_language;
            $data['MAINTENANCE_MODE'] = $request->maintenance;
        }

        // update email in env file if exist
        if ($request->filled('email', 'MAIL_HOST', 'MAIL_PASSWORD', 'MAIL_PORT', 'MAIL_ENCRYPTION')) {
            $data['MAIL_DRIVER'] = 'smtp';
            $data['MAIL_HOST'] = $request->MAIL_HOST;
            $data['MAIL_PORT'] = $request->MAIL_PORT;
            $data['MAIL_USERNAME'] = $request->email;
            $data['MAIL_PASSWORD'] = $request->MAIL_PASSWORD;
            $data['MAIL_ENCRYPTION'] = $request->MAIL_ENCRYPTION;
            $data['MAIL_FROM_ADDRESS'] = $request->email;
        }

        // update recaptcha in env file if exist
        if ($request->filled('recaptcha_site_key', 'recaptcha_secret')) {
            $data['RECAPTCHA_SITE_KEY'] = $request->recaptcha_site_key;
            $data['RECAPTCHA_SECRET_KEY'] = $request->recaptcha_secret;
        }

        // update social auth in env file if exist
        if ($request->filled('GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET')) {
            $data['GOOGLE_CLIENT_ID'] = $request->GOOGLE_CLIENT_ID;
            $data['GOOGLE_CLIENT_SECRET'] = $request->GOOGLE_CLIENT_SECRET;
        }

        if ($request->filled('FACEBOOK_CLIENT_ID', 'FACEBOOK_CLIENT_SECRET')) {
            $data['FACEBOOK_CLIENT_ID'] = $request->FACEBOOK_CLIENT_ID;
            $data['FACEBOOK_CLIENT_SECRET'] = $request->FACEBOOK_CLIENT_SECRET;
        }

        if ($request->filled('TWITTER_CLIENT_API_KEY', 'TWITTER_CLIENT_API_SECRET_KEY')) {
            $data['TWITTER_CLIENT_API_KEY'] = $request->TWITTER_CLIENT_API_KEY;
            $data['TWITTER_CLIENT_API_SECRET_KEY'] = $request->TWITTER_CLIENT_API_SECRET_KEY;
        }

        if (!empty($data)) {
            update_env($data);
        }

        foreach ($request->input() as $key => $value) {
            $option = $key;
            $setting = Setting::where('option', $option)->first();
            if ($setting !== null) {
                $setting->update(['value' => $value]);
            }
        }


        // مسح الكاش وإعادة تحميله
        Cache::forget('app_cached_data');
        Artisan::call('config:clear');

        // إعادة تسجيل البيانات المخزنة مؤقتاً
        app()->forgetInstance('cached_data');
        app()->make('cached_data');

        return redirect()->back()->with('success', __('l.Settings Updated Successfully'));
    }

    private function handleThemeUpload($themeFile)
    {
        $originalName = $themeFile->getClientOriginalName();
        $zipPath = storage_path('app/temp/' . $originalName);

        // إنشاء مجلد مؤقت
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }

        // نقل الملف المضغوط
        $themeFile->move(storage_path('app/temp'), $originalName);

        // فك الضغط
        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $extractPath = storage_path('app/temp/extract');
            $zip->extractTo($extractPath);
            $zip->close();

            // حذف الملف المضغوط
            File::delete($zipPath);

            // تعريف متغير اسم الثيم
            $themeName = null;

            // الحصول على محتويات المجلد المستخرج
            $contents = File::directories($extractPath);
            foreach ($contents as $item) {
                $itemName = basename($item);

                // إذا كان المجلد هو مجلد الثيم
                if (!in_array($itemName, ['assets'])) {
                    $themeName = $itemName;
                    $this->moveThemeFiles($extractPath, $themeName);
                    break;
                }
            }

            // بعد العثور على اسم الثيم، نتعامل مع مجلد assets
            if ($themeName !== null) {
                $this->moveThemeAssets($contents, $extractPath, $themeName);
            }

            // تنظيف المجلد المؤقت
            File::deleteDirectory($extractPath);
        }
    }

    private function moveThemeFiles($extractPath, $themeName)
    {
        $sourcePath = $extractPath . '/' . $themeName;
        $destinationPath = resource_path('views/themes/' . $themeName);

        // التأكد من وجود المجلد المصدر
        if (!File::exists($sourcePath)) {
            throw new \Exception('Theme source directory not found: ' . $sourcePath);
        }

        // إنشاء المجلد الهدف إذا لم يكن موجوداً
        if (!File::exists(resource_path('views/themes'))) {
            File::makeDirectory(resource_path('views/themes'), 0755, true);
        }

        // حذف المجلد الهدف إذا كان موجوداً
        if (File::exists($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }

        try {
            // نقل مجلد الثيم
            File::copyDirectory($sourcePath, $destinationPath);
        } catch (\Exception $e) {
            throw new \Exception('Failed to move theme directory: ' . $e->getMessage());
        }

        // البحث عن الصورة في المجلد المستخرج
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        foreach ($imageExtensions as $ext) {
            $imagePath = $extractPath . '/' . $themeName . '.' . $ext;
            if (File::exists($imagePath)) {
                // إنشاء مجلد الصور
                if (!File::exists(public_path('images/themes'))) {
                    File::makeDirectory(public_path('images/themes'), 0755, true);
                }

                // نقل الصورة
                File::copy(
                    $imagePath,
                    public_path('images/themes/' . $themeName . '.' . $ext)
                );

                // التحقق من وجود الثيم قبل إضافته
                if (!Theme::where('name', $themeName)->exists()) {
                    // إضافة الثيم في قاعدة البيانات
                    Theme::create([
                        'name' => $themeName,
                        'image' => 'images/themes/' . $themeName . '.' . $ext
                    ]);
                }
                break;
            }
        }
    }

    private function moveThemeAssets($contents, $extractPath, $themeName)
    {
        foreach ($contents as $item) {
            $itemName = basename($item);
            if ($itemName === 'assets') {
                // إنشاء مجلد assets/themes إذا لم يكن موجوداً
                if (!File::exists(public_path('assets/themes'))) {
                    File::makeDirectory(public_path('assets/themes'), 0755, true);
                }

                // نقل محتويات مجلد assets إلى المجلد الجديد باسم الثيم
                File::moveDirectory(
                    $extractPath . '/assets',
                    public_path('assets/themes/' . $themeName)
                );
                break;
            }
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');

            return redirect()->back()->with('success', __('l.Cache cleared successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('l.Error clearing cache'));
        }
    }

    public function reset(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        try {
            // حذف جميع الملفات المرفوعة
            $this->clearUploads();

            // إعادة تعيين قاعدة البيانات
            $this->resetDatabase();

            // مسح الكاش
            $this->clearAllCache();

            // إعادة تعيين الإعدادات الافتراضية
            $this->resetSettings();

            // إعادة تعيين ملفات الترجمة
            $this->resetTranslationFiles();

            return redirect()->route('login')->with('success', __('l.System has been reset successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('l.Error resetting system: ') . $e->getMessage());
        }
    }

    private function clearUploads()
    {
        // حذف الملفات القديمة للشعار والأيقونة
        $publicPath = public_path();
        $files = File::glob($publicPath . '/logo.*');
        foreach ($files as $file) {
            File::delete($file);
        }
        $publicPath = public_path();
        $files = File::glob($publicPath . '/logo-black.*');
        foreach ($files as $file) {
            File::delete($file);
        }
        $files = File::glob($publicPath . '/favicon.*');
        foreach ($files as $file) {
            File::delete($file);
        }

        // نسخ الملفات الاحتياطية
        if (File::exists($publicPath . '/logoCopy.png')) {
            File::copy($publicPath . '/logoCopy.png', $publicPath . '/logo.png');
        }
        if (File::exists($publicPath . '/logoCopy-black.png')) {
            File::copy($publicPath . '/logoCopy-black.png', $publicPath . '/logo-black.png');
        }
        if (File::exists($publicPath . '/faviconCopy.png')) {
            File::copy($publicPath . '/faviconCopy.png', $publicPath . '/favicon.png');
        }

        $uploadPaths = [
            public_path('images'),
            public_path('files'),
        ];

        // الملفات والمجلدات التي نريد الاحتفاظ بها
        $protectedItems = [
            'profile.png',
            'payment',
            'themes'
        ];

        foreach ($uploadPaths as $basePath) {
            if (!File::exists($basePath)) {
                continue;
            }

            // الحصول على جميع الملفات والمجلدات بشكل متكرر
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $path) {
                $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $path->getPathname());
                $shouldSkip = false;

                // التحقق مما إذا كان المسار يحتوي على أي من العناصر المحمية
                foreach ($protectedItems as $protectedItem) {
                    if (strpos($relativePath, $protectedItem) !== false) {
                        $shouldSkip = true;
                        break;
                    }
                }

                // تخطي العناصر المحمية
                if ($shouldSkip) {
                    continue;
                }

                // حذف الملفات فقط، وليس المجلدات
                if ($path->isFile()) {
                    File::delete($path->getPathname());
                }
            }
        }
    }

    private function resetDatabase()
    {
        // حفظ قيمة المفتاح قبل إعادة تعيين قاعدة البيانات
        $keySetting = Setting::where('option', 'key')->first();
        $keyValue = $keySetting ? $keySetting->value : null;

        // إعادة تشغيل المايجريشن
        Artisan::call('migrate:fresh');

        // تشغيل السيدر الأساسي فقط
        Artisan::call('db:seed', [
            '--class' => 'DatabaseSeeder'
        ]);

        // استعادة قيمة المفتاح إذا كانت موجودة
        if ($keyValue !== null) {
            Setting::updateOrCreate(
                ['option' => 'key'],
                ['value' => $keyValue]
            );
        }
    }

    private function clearAllCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Cache::flush();
    }

    private function resetSettings()
    {
        $domainWithProtocol = request()->getSchemeAndHttpHost();

        // تحديث ملف .env بالقيم الافتراضية
        $envData = [
            'APP_NAME' => 'dp soft',
            'APP_URL' => $domainWithProtocol,
            'APP_TIMEZONE' => 'UTC',
            'APP_LOCALE' => 'en',
            'MAINTENANCE_MODE' => 'false'
        ];

        update_env($envData);
    }

    private function resetTranslationFiles()
    {
        $languages = Language::all();

        foreach ($languages as $language) {
            $langPath = lang_path($language->code);

            if (File::exists($langPath)) {
                // أنواع ملفات الترجمة المختلفة
                $types = ['l', 'front'];

                foreach ($types as $type) {
                    $originalFile = $langPath . '/' . $type . '_original.php';
                    $translationFile = $langPath . '/' . $type . '.php';

                    // إذا كان هناك ملف أصلي، نستخدمه لإعادة تعيين ملف الترجمة
                    if (File::exists($originalFile) && File::exists($translationFile)) {
                        // نسخ محتويات الملف الأصلي إلى ملف الترجمة
                        File::copy($originalFile, $translationFile);
                    }
                }
            }
        }
    }

    public function themes($theme)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $seoPage = \App\Models\SeoPage::getBySlug('/');

        return view('themes/' . $theme . '/front.index', compact('seoPage'));
    }
}
