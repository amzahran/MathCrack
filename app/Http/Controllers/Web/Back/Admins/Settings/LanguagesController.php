<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Language;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class LanguagesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $languages = Language::all();

        return view('themes/default/back.admins.settings.languages.languages-list', ['languages' => $languages]);
    }

    public function status(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $language = Language::findOrFail(decrypt($request->id));

        // تحديث اللغة
        $language->is_active = !$language->is_active;
        $language->save();

        // مسح الكاش وإعادة تحميله
        Cache::forget('app_cached_data');
        Artisan::call('config:clear');

        return redirect()->back()->with('success', __('l.Language updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $language = Language::findOrFail(decrypt($request->id));

        $language->delete();

        return redirect()->back()->with('success', __('l.Language deleted successfully.'));
    }

    public function translate(Request $request)
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $language = Language::findOrFail(decrypt($request->id));
        $type = $request->type ?? 'l'; // نوع ملف الترجمة: l لملفات الباك، front لملفات الفرونت

        // التحقق من وجود ملف الترجمة
        $translations = $this->getTranslationFile($language->code, $type);

        // التحقق من وجود ملف الترجمات الأصلية
        $originalTranslationsPath = lang_path($language->code . '/' . $type . '_original.php');

        // إذا لم يتم العثور على ملف الترجمات الأصلية، ننشئ نسخة منه
        if (!File::exists($originalTranslationsPath) && !empty($translations)) {
            $this->saveOriginalTranslationFile($language->code, $type, $translations);
        }

        // جلب الترجمات الأصلية
        $originalTranslations = $this->getOriginalTranslationFile($language->code, $type);

        // إذا لم يتم العثور على الملف، ننشئ ملف جديد
        if (empty($translations)) {
            // إنشاء ملف فارغ بناءً على ملف اللغة الإنجليزية
            $this->createEmptyTranslationFile($language->code, $type);
            $translations = $this->getTranslationFile($language->code, $type);

            // حفظ نسخة أصلية
            $this->saveOriginalTranslationFile($language->code, $type, $translations);
            $originalTranslations = $translations;
        }

        // الحصول على الترجمات الافتراضية (الإنجليزية) للمقارنة
        $defaultTranslations = $this->getTranslationFile('en', $type);

        return view('themes/default/back.admins.settings.languages.translate', [
            'language' => $language,
            'translations' => $translations,
            'defaultTranslations' => $defaultTranslations,
            'originalTranslations' => $originalTranslations,
            'type' => $type
        ]);
    }

    public function translateStore(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $language = Language::findOrFail(decrypt($request->id));
        $type = $request->type ?? 'l';
        $translations = $request->translations;
        $resetItems = $request->reset_items ?? [];
        $resetAll = $request->reset_all == '1';

        // جلب الترجمات الأصلية
        $originalTranslations = $this->getOriginalTranslationFile($language->code, $type);

        // جلب الترجمات الحالية
        $currentTranslations = $this->getTranslationFile($language->code, $type);

        // تنظيف وتصفية البيانات المدخلة
        $filteredTranslations = [];
        if (!empty($currentTranslations)) {
            // إذا كان المطلوب هو إعادة ضبط جميع الترجمات
            if ($resetAll && !empty($originalTranslations)) {
                $filteredTranslations = $originalTranslations;
            } else {
                // نحتفظ بجميع المفاتيح الحالية
                $filteredTranslations = $currentTranslations;

                // نحدث فقط القيم التي تم تغييرها
                if (!empty($translations) && is_array($translations)) {
                    foreach ($translations as $key => $value) {
                        // تنظيف القيمة لتفادي أي مشاكل في تنسيق الملف
                        $filteredValue = $this->sanitizeTranslationValue($value);
                        $filteredTranslations[$key] = $filteredValue;
                    }
                }

                // نطبق إعادة الضبط للعناصر المطلوبة
                if (!empty($resetItems)) {
                    foreach ($resetItems as $key) {
                        if (isset($originalTranslations[$key])) {
                            $filteredTranslations[$key] = $originalTranslations[$key];
                        }
                    }
                }
            }
        }

        // حفظ الترجمات في الملف
        $success = $this->saveTranslationFile($language->code, $type, $filteredTranslations);

        if ($success) {
            if ($resetAll) {
                return redirect()->back()->with('success', __('l.All translations have been reset to original values'));
            } else {
                return redirect()->back()->with('success', __('l.Language updated successfully'));
            }
        } else {
            return redirect()->back()->with('error', __('l.Error updating language file'));
        }
    }

    /**
     * الحصول على محتوى ملف الترجمة
     */
    private function getTranslationFile($languageCode, $type = 'l')
    {
        $filePath = lang_path($languageCode . '/' . $type . '.php');

        if (File::exists($filePath)) {
            try {
                // استخدام التضمين بدلاً من File::getRequire لتجنب المشاكل مع المفاتيح ذات الأحرف الخاصة
                $fileContent = include $filePath;

                // التأكد من أن جميع القيم تمت معالجتها لتصحيح الرموز الخاصة
                if (is_array($fileContent)) {
                    foreach ($fileContent as $key => $value) {
                        $fileContent[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                    return $fileContent;
                }
                return [];
            } catch (\Throwable $e) {
                // إذا كان هناك خطأ في قراءة الملف، نقوم بقراءة محتوى الملف كنص ومعالجته يدويًا
                $content = File::get($filePath);
                return $this->parseTranslationContent($content);
            }
        }

        return [];
    }

    /**
     * الحصول على محتوى ملف الترجمة الأصلي
     */
    private function getOriginalTranslationFile($languageCode, $type = 'l')
    {
        $filePath = lang_path($languageCode . '/' . $type . '_original.php');

        if (File::exists($filePath)) {
            try {
                // استخدام التضمين بدلاً من File::getRequire لتجنب المشاكل مع المفاتيح ذات الأحرف الخاصة
                $fileContent = include $filePath;

                // التأكد من أن جميع القيم تمت معالجتها لتصحيح الرموز الخاصة
                if (is_array($fileContent)) {
                    foreach ($fileContent as $key => $value) {
                        $fileContent[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                    return $fileContent;
                }
                return [];
            } catch (\Throwable $e) {
                // إذا كان هناك خطأ في قراءة الملف، نقوم بقراءة محتوى الملف كنص ومعالجته يدويًا
                $content = File::get($filePath);
                return $this->parseTranslationContent($content);
            }
        }

        return [];
    }

    /**
     * تحليل محتوى ملف الترجمة يدويًا إذا فشلت الطريقة العادية
     */
    private function parseTranslationContent($content)
    {
        $translations = [];

        // استخراج محتوى المصفوفة بين الأقواس المعقوفة
        if (preg_match('/return\s+\[(.*?)\];/s', $content, $matches)) {
            $arrayContent = $matches[1];

            // تقسيم المحتوى إلى أزواج المفتاح والقيمة
            preg_match_all('/\s*([\'"])(.*?)\1\s*=>\s*(.*?),(?=\s*[\'"]\w|$|\s*\])/s', $arrayContent, $keyValuePairs, PREG_SET_ORDER);

            foreach ($keyValuePairs as $pair) {
                $key = $pair[2];
                $valueStr = trim($pair[3]);

                // إزالة علامات الاقتباس والتعامل مع القيم المقتبسة
                if (preg_match('/^[\'"](.*)[\'"]\s*$/', $valueStr, $valueMatch)) {
                    $value = $valueMatch[1];
                    // إرجاع الأحرف المهربة إلى طبيعتها
                    $value = stripcslashes($value);
                    // تصحيح الرموز الخاصة
                    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                } else {
                    // إذا كانت القيمة ليست مقتبسة (مثلاً رقم أو ثابت)
                    $value = $valueStr;
                }

                $translations[$key] = $value;
            }
        }

        return $translations;
    }

    /**
     * تصفية قيمة الترجمة لتفادي مشاكل في تنسيق الملف
     */
    private function sanitizeTranslationValue($value)
    {
        // حل مشكلة الرموز الخاصة مثل &
        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * حفظ الترجمات في ملف
     */
    private function saveTranslationFile($languageCode, $type, $translations)
    {
        try {
            $filePath = lang_path($languageCode . '/' . $type . '.php');

            // إنشاء محتوى الملف بطريقة أكثر أماناً
            $fileContent = "<?php\n\nreturn [\n";

            foreach ($translations as $key => $value) {
                // استخدام دالة التحويل الحرفي للنص لتجنب مشاكل علامات الاقتباس
                $formattedKey = $this->formatForPHPArray($key);
                $formattedValue = $this->formatForPHPArray($value);

                $fileContent .= "    {$formattedKey} => {$formattedValue},\n";
            }

            $fileContent .= "];\n";

            // كتابة الملف
            File::put($filePath, $fileContent);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * تنسيق النص ليكون آمناً في ملف ترجمة PHP
     */
    private function formatForPHPArray($string)
    {
        // استخدام مزدوج من علامات الاقتباس لسهولة التعامل مع علامات الاقتباس المفردة
        $formatted = '"' . str_replace(
            ['"', '$', '\\'],
            ['\"', '\$', '\\\\'],
            $string
        ) . '"';

        return $formatted;
    }

    /**
     * الحصول على مسار ملف اللغة والتأكد من وجود المجلد
     */
    private function ensureLanguageDirectory($languageCode)
    {
        $langPath = lang_path($languageCode);
        if (!File::exists($langPath)) {
            File::makeDirectory($langPath, 0755, true);
        }
        return $langPath;
    }

    /**
     * إنشاء ملف ترجمة فارغ بناءً على ملف اللغة الإنجليزية
     */
    private function createEmptyTranslationFile($languageCode, $type = 'l')
    {
        // التأكد من وجود مجلد اللغة
        $this->ensureLanguageDirectory($languageCode);

        // الحصول على ترجمات اللغة الإنجليزية
        $defaultTranslations = $this->getTranslationFile('en', $type);

        // إنشاء ملف ترجمة فارغ يستخدم نفس المفاتيح
        $emptyTranslations = [];
        foreach ($defaultTranslations as $key => $value) {
            $emptyTranslations[$key] = $value; // نسخ القيمة الإنجليزية كافتراضي
        }

        // حفظ الملف
        $this->saveTranslationFile($languageCode, $type, $emptyTranslations);
    }

    /**
     * حفظ نسخة من الترجمات الأصلية
     */
    private function saveOriginalTranslationFile($languageCode, $type, $translations)
    {
        try {
            $filePath = lang_path($languageCode . '/' . $type . '_original.php');

            // إنشاء محتوى الملف بطريقة أكثر أماناً
            $fileContent = "<?php\n\nreturn [\n";

            foreach ($translations as $key => $value) {
                // استخدام دالة التحويل الحرفي للنص لتجنب مشاكل علامات الاقتباس
                $formattedKey = $this->formatForPHPArray($key);
                $formattedValue = $this->formatForPHPArray($value);

                $fileContent .= "    {$formattedKey} => {$formattedValue},\n";
            }

            $fileContent .= "];\n";

            // كتابة الملف
            File::put($filePath, $fileContent);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
