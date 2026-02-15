<?php

namespace App\Http\Controllers\Web\Back\Admins\Pages\Blogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\Setting;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Language;
use Stichoza\GoogleTranslate\GoogleTranslate;

class BlogCategoriesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        if ($request->ajax()) {
            $categories = BlogCategory::query();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('name', function ($category) {
                    $defaultLanguage = app('cached_data')['settings']['default_language'];
                    return is_array($category->name) ? ($category->name[$defaultLanguage] ?? '') : $category->name;
                })
                ->addColumn('slug', function ($category) {
                    return $category->slug;
                })
                ->addColumn('action', function ($category) {
                    $id = encrypt($category->id);
                    $actions = '<div class="d-flex gap-2">';

                    if (Gate::allows('edit blog_category')) {
                        $actions .= '
                        <a href="' . route('dashboard.admins.blogs.categories-edit', ['id' => $id]) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit ti-xs"></i>
                        </a>
                        <a href="' . route('dashboard.admins.blogs.categories-get-translations') . '?id=' . encrypt($category->id) . '" class="btn btn-sm btn-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g fill="currentColor">
                                    <path d="M7.75 2.75a.75.75 0 0 0-1.5 0v1.258a32.987 32.987 0 0 0-3.599.278a.75.75 0 1 0 .198 1.487A31.545 31.545 0 0 1 8.7 5.545A19.381 19.381 0 0 1 7 9.56a19.418 19.418 0 0 1-1.002-2.05a.75.75 0 0 0-1.384.577a20.935 20.935 0 0 0 1.492 2.91a19.613 19.613 0 0 1-3.828 4.154a.75.75 0 1 0 .945 1.164A21.116 21.116 0 0 0 7 12.331c.095.132.192.262.29.391a.75.75 0 0 0 1.194-.91a18.97 18.97 0 0 1-.59-.815a20.888 20.888 0 0 0 2.333-5.332c.31.031.618.068.924.108a.75.75 0 0 0 .198-1.487a32.832 32.832 0 0 0-3.599-.278V2.75Z"></path>
                                    <path fill-rule="evenodd" d="M13 8a.75.75 0 0 1 .671.415l4.25 8.5a.75.75 0 1 1-1.342.67L15.787 16h-5.573l-.793 1.585a.75.75 0 1 1-1.342-.67l4.25-8.5A.75.75 0 0 1 13 8Zm2.037 6.5L13 10.427L10.964 14.5h4.073Z" clip-rule="evenodd"></path>
                                </g>
                            </svg>
                        </a>';
                    }

                    if (Gate::allows('delete blog_category')) {
                        $actions .= '
                        <button type="button" class="btn btn-sm btn-danger delete-category" data-id="' . $id . '">
                            <i class="fa fa-trash ti-xs"></i>
                        </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $defaultLanguage = app('cached_data')['settings']['default_language'];
                    $query->where(function ($q) use ($keyword, $defaultLanguage) {
                        $q->whereRaw("JSON_EXTRACT(name, '$.\"{$defaultLanguage}\"') LIKE ?", ["%{$keyword}%"]);
                    });
                })
                ->orderColumn('name', function ($query, $order) {
                    $defaultLanguage = app('cached_data')['settings']['default_language'];
                    $query->orderByRaw("JSON_EXTRACT(name, '$.\"{$defaultLanguage}\"') {$order}");
                })
                ->filterColumn('slug', function ($query, $keyword) {
                    $query->where('slug', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('slug', function ($query, $order) {
                    $query->orderBy('slug', $order);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('themes.default.back.admins.pages.blogs.categories-list');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add blog_category')) {
            return view('themes.default.back.permission-denied');
        }
        $request->validate([
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'auto_translate' => 'nullable|in:on',
        ]);

        $cachedData = app('cached_data');
        $defaultLanguage = $cachedData['settings']['default_language'];

        $category = BlogCategory::create([
            'name' => [$defaultLanguage => $request->input('name')],
            'slug' => BlogCategory::createUniqueSlug($request->input('name')),
            'meta_keywords' => [$defaultLanguage => $request->input('meta_keywords')],
            'meta_description' => [$defaultLanguage => $request->input('meta_description')],
        ]);

        if ($request->input('auto_translate')) {
            $request->merge(['id' => encrypt($category->id)]);
            $this->autoTranslate($request);
        }

        return redirect()->back()->with('success', __('l.Category added successfully'));
    }

    public function getTranslations(Request $request)
    {
        if (!Gate::allows('edit blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        $category = BlogCategory::findOrFail(decrypt($request->id));
        $languages = Language::where('is_active', 1)->get();

        return view('themes.default.back.admins.pages.blogs.categories-translations', compact('category', 'languages'));
    }

    public function translate(Request $request)
    {
        if (!Gate::allows('edit blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        // تحقق من جميع حقول الترجمة
        $validationRules = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'name-')) {
                $validationRules[$key] = 'required|string|max:255';
            }
            // قواعد التحقق من حقول الميتا
            if (str_starts_with($key, 'meta_keywords-')) {
                $validationRules[$key] = 'nullable|string|max:255';
            }
            if (str_starts_with($key, 'meta_description-')) {
                $validationRules[$key] = 'nullable|string|max:160';
            }
        }

        $request->validate($validationRules);

        $category = BlogCategory::findOrFail(decrypt($request->id));
        $currentName = is_array($category->name) ? $category->name : [];
        $currentMetaKeywords = is_array($category->meta_keywords) ? $category->meta_keywords : [];
        $currentMetaDescription = is_array($category->meta_description) ? $category->meta_description : [];

        $translationsName = [];
        $translationsMetaKeywords = [];
        $translationsMetaDescription = [];

        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'name-')) {
                $languageCode = str_replace('name-', '', $key);
                $translationsName[$languageCode] = $value;
            }
            if (str_starts_with($key, 'meta_keywords-')) {
                $languageCode = str_replace('meta_keywords-', '', $key);
                $translationsMetaKeywords[$languageCode] = $value;
            }
            if (str_starts_with($key, 'meta_description-')) {
                $languageCode = str_replace('meta_description-', '', $key);
                $translationsMetaDescription[$languageCode] = $value;
            }
        }

        $category->update([
            'name' => array_merge($currentName, $translationsName),
            'meta_keywords' => array_merge($currentMetaKeywords, $translationsMetaKeywords),
            'meta_description' => array_merge($currentMetaDescription, $translationsMetaDescription),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    public function autoTranslate(Request $request)
    {
        if (!Gate::allows('edit blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        $category = BlogCategory::findOrFail(decrypt($request->id));
        $currentName = is_array($category->name) ? $category->name : [];
        $currentMetaKeywords = is_array($category->meta_keywords) ? $category->meta_keywords : [];
        $currentMetaDescription = is_array($category->meta_description) ? $category->meta_description : [];

        $cachedData = app('cached_data');
        $sourceLanguage = $cachedData['settings']['default_language'];

        $languages = Language::where('is_active', 1)
            ->where('code', '!=', $sourceLanguage)
            ->get();

        $translationsName = [];
        $translationsMetaKeywords = [];
        $translationsMetaDescription = [];
        $translator = new GoogleTranslate();

        foreach ($languages as $language) {
            try {
                // ترجمة الاسم
                $translatedName = $translator
                    ->setSource($sourceLanguage)
                    ->setTarget($language->code)
                    ->translate($category->getTranslation('name', $sourceLanguage));

                $translationsName[$language->code] = $translatedName;

                // ترجمة الكلمات المفتاحية إذا كانت موجودة
                if ($category->getTranslation('meta_keywords', $sourceLanguage)) {
                    $sourceKeywords = $category->getTranslation('meta_keywords', $sourceLanguage);

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

                            // تحويل المصفوفة المترجمة إلى JSON
                            $translationsMetaKeywords[$language->code] = json_encode($translatedKeywordsArray);
                        } else {
                            // إذا لم تكن مصفوفة، ترجم النص كاملاً
                            $translatedMetaKeywords = $translator
                                ->setSource($sourceLanguage)
                                ->setTarget($language->code)
                                ->translate($sourceKeywords);

                            $translationsMetaKeywords[$language->code] = $translatedMetaKeywords;
                        }
                    } catch (\Exception $e) {
                        // إذا فشل تحليل JSON، ترجم النص كاملاً
                        $translatedMetaKeywords = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceKeywords);

                        $translationsMetaKeywords[$language->code] = $translatedMetaKeywords;
                    }
                }

                // ترجمة الميتا ديسكربشن إذا كانت موجودة
                if ($category->meta_description && $category->getTranslation('meta_description', $sourceLanguage, false)) {
                    $sourceMetaDescription = $category->getTranslation('meta_description', $sourceLanguage, false);
                    if (!empty($sourceMetaDescription)) {
                        $translatedMetaDescription = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceMetaDescription);
                        $translationsMetaDescription[$language->code] = $translatedMetaDescription;
                    }
                }

                // تأخير قصير لتجنب تجاوز حد طلبات Google Translate
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                continue;
            }
        }

        $category->update([
            'name' => array_merge($currentName, $translationsName),
            'meta_keywords' => array_merge($currentMetaKeywords, $translationsMetaKeywords),
            'meta_description' => array_merge($currentMetaDescription, $translationsMetaDescription),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        $category = BlogCategory::findOrFail(decrypt($request->id));

        return view('themes.default.back.admins.pages.blogs.categories-edit', compact('category'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $category = BlogCategory::findOrFail(decrypt($request->id));
        $currentName = is_array($category->name) ? $category->name : [];
        $currentMetaKeywords = is_array($category->meta_keywords) ? $category->meta_keywords : [];
        $currentMetaDescription = is_array($category->meta_description) ? $category->meta_description : [];
        $defaultLanguage = Setting::where('option', 'default_language')->first()->value;

        $category->update([
            'name' => array_merge($currentName, [$defaultLanguage => $request->name]),
            'slug' => BlogCategory::createUniqueSlug($request->input('name'), $category->id),
            'meta_keywords' => array_merge($currentMetaKeywords, [$defaultLanguage => $request->meta_keywords]),
            'meta_description' => array_merge($currentMetaDescription, [$defaultLanguage => $request->meta_description]),
        ]);

        return redirect()->back()->with('success', __('l.Category updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        $category = BlogCategory::findOrFail(decrypt($request->id));

        $category->delete();

        return redirect()->back()->with('success', __('l.Category deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete blog_category')) {
            return view('themes.default.back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        BlogCategory::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', __('l.Categories deleted successfully'));
    }
}
