<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class TaxesController extends Controller
{
    /**
     * عرض قائمة ضرائب
     */
    public function index()
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $taxes = Tax::get();
        return view('themes/default/back.admins.settings.taxes.taxes-list', compact('taxes'));
    }

    /**
     * إنشاء ضريبة
     */
    public function store(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:fixed,percentage',
            'rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'auto_translate' => 'nullable|boolean',
        ]);

        $defaultLanguage = app('cached_data')['settings']['default_language'];

        $tax = Tax::create([
            'name' => [$defaultLanguage => $request->name],
            'type' => $request->type,
            'rate' => $request->rate,
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($request->auto_translate) {
            $request->merge(['id' => encrypt($tax->id)]);
            $this->autoTranslate($request);
        }

        return redirect()->back()->with('success', __('l.Tax Created Successfully'));
    }


    /**
     * عرض نموذج تعديل ضريبة
     */
    public function edit(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $tax = Tax::findOrFail(decrypt($request->id));
        return view('themes/default/back.admins.settings.taxes.taxes-edit', compact('tax'));
    }

    /**
     * تحديث ضريبة
     */
    public function update(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $tax = Tax::findOrFail(decrypt($request->id));

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:fixed,percentage',
            'rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
        ]);

        $defaultLanguage = app('cached_data')['settings']['default_language'];

        $currentName = is_array($tax->name) ? $tax->name : [];

        $tax->update([
            'name' => array_merge($currentName, [$defaultLanguage => $request->name]),
            'type' => $request->type,
            'rate' => $request->rate,
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->back()->with('success', __('l.Tax Updated Successfully'));
    }

    /**
     * عرض الترجمات
     */
    public function getTranslations(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes.default.back.permission-denied');
        }

        $tax = Tax::find(decrypt($request->id));
        $languages = Language::where('is_active', 1)->get();

        return view('themes.default.back.admins.settings.taxes.taxes-translations', compact('tax', 'languages'));
    }

    /**
     * إرسال طلبات الترجمة
     */
    public function translate(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $tax = Tax::findOrFail(decrypt($request->id));
        $currentName = is_array($tax->name) ? $tax->name : [];

        $translationsName = [];
        $hasTranslations = false;

        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'name-')) {
                $languageCode = str_replace('name-', '', $key);
                $translationsName[$languageCode] = $value;
                $hasTranslations = true;
            }
        }

        if (!$hasTranslations) {
            return redirect()->back()->with('error', __('l.No translation data provided'));
        }

        $tax->update([
            'name' => array_merge($currentName, $translationsName),
        ]);

        return redirect()->back()->with('success', __('l.Tax Updated Successfully'));
    }

    /**
     * ترجمة تلقائية
     */
    public function autoTranslate(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes.default.back.permission-denied');
        }

        $tax = Tax::findOrFail(decrypt($request->id));
        $cachedData = app('cached_data');
        $sourceLanguage = $cachedData['settings']['default_language'];

        $languages = Language::where('is_active', 1)
            ->where('code', '!=', $sourceLanguage)
            ->get();

        $translator = new GoogleTranslate();

        foreach ($languages as $language) {
            try {
                // ترجمة name إذا كان موجوداً
                if ($tax->hasTranslation('name', $sourceLanguage)) {
                    $sourceName = $tax->getTranslation('name', $sourceLanguage);
                    if (!empty($sourceName)) {
                        $translatedName = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceName);

                        $tax->setTranslation('name', $language->code, $translatedName);
                    }
                }

                // تأخير قصير لتجنب تجاوز حد طلبات Google Translate
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                Log::error('Translation error for language ' . $language->code . ': ' . $e->getMessage());
                continue;
            }
        }

        $tax->save();

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    /**
     * حذف ضريبة
     */
    public function delete(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $tax = Tax::findOrFail(decrypt($request->id));
        $tax->delete();

        return redirect()->back()->with('success', __('l.Tax Deleted Successfully'));
    }
}
