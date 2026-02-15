<?php

namespace App\Http\Controllers\Web\Back\Admins\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Gate;
use App\Models\Language;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class PagesController extends Controller
{
    public function index(Request $request, $page = null)
    {
        if (!Gate::allows('show pages')) {
            return view('themes.default.back.permission-denied');
        }

        if ($page) {
            $page = Page::where('title', $page)->firstOrFail();
            return view('themes.default.back.admins.pages.pages', compact('page'));
        } else {
            abort(404);
        }
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit pages')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'content' => 'required|string|max:1000000',
        ]);

        $page = Page::findOrFail(decrypt($request->id));

        $defaultLanguage = app('cached_data')['settings']['default_language'];

        $currentContent = is_array($page->content) ? $page->content : [];
        $page->update([
            'content' => array_merge($currentContent, [$defaultLanguage => $request->content]),
        ]);

        return redirect()->back()->with('success', __('l.Page updated successfully'));
    }

    public function getTranslations(Request $request)
    {
        if (!Gate::allows('edit pages')) {
            return view('themes.default.back.permission-denied');
        }

        $page = Page::find(decrypt($request->id));
        $languages = Language::where('is_active', 1)->get();

        return view('themes.default.back.admins.pages.pages-translations', compact('page', 'languages'));
    }

    public function translate(Request $request)
    {
        if (!Gate::allows('edit pages')) {
            return view('themes.default.back.permission-denied');
        }

        // تحقق من جميع حقول الترجمة
        $validationRules = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'content-')) {
                $validationRules[$key] = 'required|string|max:1000000';
            }
        }

        $request->validate($validationRules);

        $page = Page::findOrFail(decrypt($request->id));
        $currentContent = is_array($page->content) ? $page->content : [];

        $translationsContent = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'content-')) {
                $languageCode = str_replace('content-', '', $key);
                $translationsContent[$languageCode] = $value;
            }
        }

        $page->update([
            'content' => array_merge($currentContent, $translationsContent),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    public function autoTranslate(Request $request)
    {
        if (!Gate::allows('edit pages')) {
            return view('themes.default.back.permission-denied');
        }

        $page = Page::findOrFail(decrypt($request->id));
        $currentContent = is_array($page->content) ? $page->content : [];

        $cachedData = app('cached_data');
        $sourceLanguage = $cachedData['settings']['default_language'];

        $languages = Language::where('is_active', 1)
            ->where('code', '!=', $sourceLanguage)
            ->get();

        $translationsContent = [];
        $translator = new GoogleTranslate();

        foreach ($languages as $language) {
            try {
                // ترجمة المحتوى
                $translatedContent = $translator
                    ->setSource($sourceLanguage)
                    ->setTarget($language->code)
                    ->translate($page->getTranslation('content', $sourceLanguage));

                $translationsContent[$language->code] = $translatedContent;

                // تأخير قصير لتجنب تجاوز حد طلبات Google Translate
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                Log::error('Translation error for language ' . $language->code . ': ' . $e->getMessage());
                continue;
            }
        }

        $page->update([
            'content' => array_merge($currentContent, $translationsContent),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }
}
