<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class SeoController extends Controller
{
    /**
     * عرض قائمة صفحات SEO
     */
    public function index()
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $seoPages = SeoPage::get();
        return view('themes/default/back.admins.settings.seo.seo-list', compact('seoPages'));
    }

    /**
     * عرض صفحة SEO
     */
    public function show(Request $request)
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $seoPage = SeoPage::findOrFail(decrypt($request->id));
        return view('themes/default/back.admins.settings.seo.seo-show', compact('seoPage'));
    }

    /**
     * عرض نموذج تعديل صفحة SEO
     */
    public function edit(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $seoPage = SeoPage::findOrFail(decrypt($request->id));
        return view('themes/default/back.admins.settings.seo.seo-edit', compact('seoPage'));
    }

    /**
     * تحديث صفحة SEO
     */
    public function update(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $seoPage = SeoPage::findOrFail(decrypt($request->id));

        $request->validate([
            'title' => 'nullable|string|max:255',
           'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'structured_data' => 'nullable|json',
        ]);


        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('og_image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($seoPage->og_image) {
                $oldImagePath = public_path($seoPage->og_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('og_image');
            $imageName = 'seo-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/seo'), $imageName);
            $seoData['og_image'] = 'images/seo/' . $imageName;
        }

        $defaultLanguage = app('cached_data')['settings']['default_language'];

        $keywordsData = json_decode($request->keywords, true);
        $metakeywords = '';

        if (is_array($keywordsData)) {
            $keywordValues = [];
            foreach ($keywordsData as $keyword) {
                if (isset($keyword['value'])) {
                    $keywordValues[] = $keyword['value'];
                }
            }
            $metakeywords = implode(', ', $keywordValues);
        } else {
            $metakeywords = $request->keywords;
        }

        $currentTitle = is_array($seoPage->title) ? $seoPage->title : [];
        $currentDescription = is_array($seoPage->description) ? $seoPage->description : [];
        $currentKeywords = is_array($seoPage->keywords) ? $seoPage->keywords : [];
        $currentOgTitle = is_array($seoPage->og_title) ? $seoPage->og_title : [];
        $currentOgDescription = is_array($seoPage->og_description) ? $seoPage->og_description : [];

        $seoPage->update([
            'title' => array_merge($currentTitle, [$defaultLanguage => $request->title]),
            'description' => array_merge($currentDescription, [$defaultLanguage => $request->description]),
            'keywords' => array_merge($currentKeywords, [$defaultLanguage => $metakeywords]),
            'og_title' => array_merge($currentOgTitle, [$defaultLanguage => $request->og_title]),
            'og_description' => array_merge($currentOgDescription, [$defaultLanguage => $request->og_description]),
            'og_image' => $request->og_image,
            'structured_data' => $request->structured_data,
        ]);

        return redirect()->back()->with('success', __('l.SEO Page Updated Successfully'));
    }

    public function getTranslations(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes.default.back.permission-denied');
        }

        $seoPage = SeoPage::find(decrypt($request->id));
        $languages = Language::where('is_active', 1)->get();

        return view('themes.default.back.admins.settings.seo.seo-translations', compact('seoPage', 'languages'));
    }

    /**
     * إرسال طلبات الترجمة
     */
    public function translate(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $seoPage = SeoPage::findOrFail(decrypt($request->id));

        $currentTitle = is_array($seoPage->title) ? $seoPage->title : [];
        $currentDescription = is_array($seoPage->description) ? $seoPage->description : [];
        $currentKeywords = is_array($seoPage->keywords) ? $seoPage->keywords : [];
        $currentOgTitle = is_array($seoPage->og_title) ? $seoPage->og_title : [];
        $currentOgDescription = is_array($seoPage->og_description) ? $seoPage->og_description : [];

        $translationsTitle = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'title-')) {
                $languageCode = str_replace('title-', '', $key);
                $translationsTitle[$languageCode] = $value;
            }
        }

        $translationsDescription = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'description-')) {
                $languageCode = str_replace('description-', '', $key);
                $translationsDescription[$languageCode] = $value;
            }
        }

        $translationsKeywords = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'keywords-')) {
                $languageCode = str_replace('keywords-', '', $key);
                $translationsKeywords[$languageCode] = $value;
            }
        }

        $translationsOgTitle = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'og_title-')) {
                $languageCode = str_replace('og_title-', '', $key);
                $translationsOgTitle[$languageCode] = $value;
            }
        }

        $translationsOgDescription = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'og_description-')) {
                $languageCode = str_replace('og_description-', '', $key);
                $translationsOgDescription[$languageCode] = $value;
            }
        }

        $seoPage->update([
            'title' => array_merge($currentTitle, $translationsTitle),
            'description' => array_merge($currentDescription, $translationsDescription),
            'keywords' => array_merge($currentKeywords, $translationsKeywords),
            'og_title' => array_merge($currentOgTitle, $translationsOgTitle),
            'og_description' => array_merge($currentOgDescription, $translationsOgDescription),
        ]);

        return redirect()->back()->with('success', __('l.SEO Page Updated Successfully'));
    }


    public function autoTranslate(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes.default.back.permission-denied');
        }

        $seoPage = SeoPage::findOrFail(decrypt($request->id));
        $cachedData = app('cached_data');
        $sourceLanguage = $cachedData['settings']['default_language'];

        $languages = Language::where('is_active', 1)
            ->where('code', '!=', $sourceLanguage)
            ->get();

        $translator = new GoogleTranslate();

        foreach ($languages as $language) {
            try {
                // ترجمة title إذا كان موجوداً
                if ($seoPage->hasTranslation('title', $sourceLanguage)) {
                    $sourceTitle = $seoPage->getTranslation('title', $sourceLanguage);
                    if (!empty($sourceTitle)) {
                        $translatedTitle = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceTitle);

                        $seoPage->setTranslation('title', $language->code, $translatedTitle);
                    }
                }

                // ترجمة description إذا كان موجوداً
                if ($seoPage->hasTranslation('description', $sourceLanguage)) {
                    $sourceDescription = $seoPage->getTranslation('description', $sourceLanguage);
                    if (!empty($sourceDescription)) {
                        $translatedDescription = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceDescription);

                        $seoPage->setTranslation('description', $language->code, $translatedDescription);
                    }
                }

                // ترجمة الكلمات المفتاحية إذا كانت موجودة
                if ($seoPage->getTranslation('keywords', $sourceLanguage)) {
                    $sourceKeywords = $seoPage->getTranslation('keywords', $sourceLanguage);

                    // التحقق مما إذا كانت الكلمات المفتاحية بتنسيق JSON
                    $keywordsArray = [];
                    try {
                        // محاولة تحليل البيانات كـ JSON
                        $keywordsData = json_decode($sourceKeywords, true);
                        if (is_array($keywordsData)) {
                            // استخراج القيم من الكائنات وترجمتها
                            $translatedKeywordsArray = [];
                            foreach ($keywordsData as $keyword) {
                                if (isset($keyword['value'])) {
                                    $translatedValue = $translator
                                        ->setSource($sourceLanguage)
                                        ->setTarget($language->code)
                                        ->translate($keyword['value']);

                                    $translatedKeywordsArray[] = ['value' => $translatedValue];
                                }
                            }
                            // حفظ الكلمات المفتاحية المترجمة كمصفوفة (ستُخزن كـ JSON تلقائياً)
                            $seoPage->setTranslation('keywords', $language->code, json_encode($translatedKeywordsArray));
                        } else {
                            // إذا لم تكن مصفوفة، ترجم النص كاملاً
                            $translatedMetaKeywords = $translator
                                ->setSource($sourceLanguage)
                                ->setTarget($language->code)
                                ->translate($sourceKeywords);

                            $seoPage->setTranslation('keywords', $language->code, $translatedMetaKeywords);
                        }
                    } catch (\Exception $e) {
                        // إذا فشل تحليل JSON، ترجم النص كاملاً
                        $translatedMetaKeywords = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceKeywords);

                        $seoPage->setTranslation('keywords', $language->code, $translatedMetaKeywords);
                    }
                }

                // ترجمة og_title إذا كان موجوداً
                if ($seoPage->hasTranslation('og_title', $sourceLanguage)) {
                    $sourceOgTitle = $seoPage->getTranslation('og_title', $sourceLanguage);
                    if (!empty($sourceOgTitle)) {
                        $translatedOgTitle = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceOgTitle);

                        $seoPage->setTranslation('og_title', $language->code, $translatedOgTitle);
                    }
                }

                // ترجمة og_description إذا كان موجوداً
                if ($seoPage->hasTranslation('og_description', $sourceLanguage)) {
                    $sourceOgDescription = $seoPage->getTranslation('og_description', $sourceLanguage);
                    if (!empty($sourceOgDescription)) {
                        $translatedOgDescription = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceOgDescription);

                        $seoPage->setTranslation('og_description', $language->code, $translatedOgDescription);
                    }
                }

                // تأخير قصير لتجنب تجاوز حد طلبات Google Translate
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                Log::error('Translation error for language ' . $language->code . ': ' . $e->getMessage());
                continue;
            }
        }

        $seoPage->save();

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }
}