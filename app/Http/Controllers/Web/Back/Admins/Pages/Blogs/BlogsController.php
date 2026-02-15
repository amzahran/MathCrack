<?php

namespace App\Http\Controllers\Web\Back\Admins\Pages\Blogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use App\Models\Setting;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;

class BlogsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show blog')) {
            return view('themes.default.back.permission-denied');
        }
        $defaultLanguage = app('cached_data')['settings']['default_language'];
        $categories = BlogCategory::all();
        if ($request->ajax()) {
            $blogs = Blog::with(['category', 'user'])
                ->select('blogs.*')
                ->leftJoin('blog_categories', 'blogs.blog_category_id', '=', 'blog_categories.id')
                ->leftJoin('users', 'blogs.user_id', '=', 'users.id');

            if (!Gate::allows('access all blog')) {
                $blogs->where('blogs.user_id', Auth::id());
            }

            return DataTables::of($blogs)
                ->addIndexColumn()
                ->addColumn('title', function ($blog) use ($defaultLanguage) {
                    $title = htmlspecialchars($blog->getTranslation('title', $defaultLanguage, false), ENT_QUOTES, 'UTF-8');
                    $imageHtml = '';

                    if ($blog->image && file_exists(public_path($blog->image))) {
                        $imageHtml = '<div class="avatar avatar-sm me-2">
                            <img src="' . asset($blog->image) . '" alt="Blog Image" class="rounded-circle">
                        </div>';
                    } else {
                        $imageHtml = '<div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded-circle bg-label-primary">' .
                            mb_substr($title, 0, 1, 'UTF-8') .
                            '</span>
                        </div>';
                    }

                    return '<div class="d-flex align-items-center">
                        ' . $imageHtml . '
                        <div class="blog-title">' . $title . '</div>
                    </div>';
                })
                ->addColumn('category', function ($blog) use ($defaultLanguage) {
                    if (!$blog->category) {
                        return '<span class="badge bg-label-danger">' . __('l.Uncategorized') . '</span>';
                    }
                    $categoryName = htmlspecialchars($blog->category->getTranslation('name', $defaultLanguage, false), ENT_QUOTES, 'UTF-8');
                    return '<span class="badge bg-label-primary">' . $categoryName . '</span>';
                })
                ->addColumn('author', function ($blog) {
                    if (!$blog->user) {
                        return '<div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-label-danger">?</span>
                            </div>
                            <div>' . __('l.Deleted User') . '</div>
                        </div>';
                    }

                    $imageHtml = '';

                    if ($blog->user->photo) {
                        $imageHtml = '<img src="' . asset($blog->user->photo) . '" alt="User Avatar" class="rounded-circle">';
                    } else {
                        $imageHtml = '<span class="avatar-initial rounded-circle bg-label-info">' .
                            strtoupper(substr($blog->user->firstname, 0, 1)) .
                            '</span>';
                    }

                    return '<div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            ' . $imageHtml . '
                        </div>
                        <div>' . e($blog->user->firstname . ' ' . $blog->user->lastname) . '</div>
                    </div>';
                })
                ->addColumn('views', function ($blog) {
                    return '<span class="text-primary"><i class="fa fa-eye me-1"></i>' . $blog->views . '</span>';
                })
                ->addColumn('status', function ($blog) {
                    return '<span class="badge bg-label-' . ($blog->status == 'published' ? 'success' : 'warning') . '">' . __('l.' . ucfirst($blog->status)) . '</span>';
                })
                ->addColumn('created_at', function ($blog) {
                    return '<span class="text-muted">' . $blog->created_at->format('Y-m-d') . '</span>';
                })
                ->addColumn('updated_at', function ($blog) {
                    return '<span class="text-muted">' . $blog->updated_at->format('Y-m-d') . '</span>';
                })
                ->addColumn('action', function ($blog) {
                    $id = encrypt($blog->id);
                    $actions = '<div class="d-flex gap-2 justify-content-end">';

                    $actions .= '
                        <a href="' . route('dashboard.admins.blogs.articles-show', ['id' => $id]) . '" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="' . __('l.Show') . '">
                            <i class="fa fa-eye ti-xs"></i>
                        </a>';

                    if (Gate::allows('edit blog')) {
                        $actions .= '
                        <a href="' . route('dashboard.admins.blogs.articles-edit', ['id' => $id]) . '" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="' . __('l.Edit') . '">
                            <i class="fa fa-edit ti-xs"></i>
                        </a>
                        <a href="' . route('dashboard.admins.blogs.articles-get-translations') . '?id=' . encrypt($blog->id) . '" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="' . __('l.Translations') . '">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g fill="currentColor">
                                    <path d="M7.75 2.75a.75.75 0 0 0-1.5 0v1.258a32.987 32.987 0 0 0-3.599.278a.75.75 0 1 0 .198 1.487A31.545 31.545 0 0 1 8.7 5.545A19.381 19.381 0 0 1 7 9.56a19.418 19.418 0 0 1-1.002-2.05a.75.75 0 0 0-1.384.577a20.935 20.935 0 0 0 1.492 2.91a19.613 19.613 0 0 1-3.828 4.154a.75.75 0 1 0 .945 1.164A21.116 21.116 0 0 0 7 12.331c.095.132.192.262.29.391a.75.75 0 0 0 1.194-.91a18.97 18.97 0 0 1-.59-.815a20.888 20.888 0 0 0 2.333-5.332c.31.031.618.068.924.108a.75.75 0 0 0 .198-1.487a32.832 32.832 0 0 0-3.599-.278V2.75Z"></path>
                                    <path fill-rule="evenodd" d="M13 8a.75.75 0 0 1 .671.415l4.25 8.5a.75.75 0 1 1-1.342.67L15.787 16h-5.573l-.793 1.585a.75.75 0 1 1-1.342-.67l4.25-8.5A.75.75 0 0 1 13 8Zm2.037 6.5L13 10.427L10.964 14.5h4.073Z" clip-rule="evenodd"></path>
                                </g>
                            </svg>
                        </a>';
                    }

                    if (Gate::allows('delete blog')) {
                        $actions .= '
                        <button type="button" class="btn btn-sm btn-danger delete-article" data-id="' . $id . '" data-bs-toggle="tooltip" title="' . __('l.Delete') . '">
                            <i class="fa fa-trash ti-xs"></i>
                        </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->filterColumn('title', function($query, $keyword) use ($defaultLanguage) {
                    $query->whereRaw("JSON_EXTRACT(blogs.title, '$." . $defaultLanguage . "') like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('category', function($query, $keyword) use ($defaultLanguage) {
                    $query->where(function($q) use ($keyword, $defaultLanguage) {
                        $q->whereHas('category', function($q) use ($keyword, $defaultLanguage) {
                            $q->whereRaw("JSON_EXTRACT(name, '$." . $defaultLanguage . "') like ?", ["%{$keyword}%"]);
                        })->orWhereDoesntHave('category');
                    });
                })
                ->filterColumn('author', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->whereHas('user', function($q) use ($keyword) {
                            $q->where('firstname', 'like', "%{$keyword}%")
                               ->orWhere('lastname', 'like', "%{$keyword}%");
                        })->orWhereDoesntHave('user');
                    });
                })
                ->filterColumn('views', function($query, $keyword) {
                    $query->where('views', 'like', "%{$keyword}%");
                })
                ->filterColumn('status', function($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })
                ->filterColumn('created_at', function($query, $keyword) {
                    $query->where('blogs.created_at', 'like', "%{$keyword}%");
                })
                ->filterColumn('updated_at', function($query, $keyword) {
                    $query->where('blogs.updated_at', 'like', "%{$keyword}%");
                })
                ->orderColumn('title', function ($query, $order) use ($defaultLanguage) {
                    $query->orderByRaw("JSON_EXTRACT(blogs.title, '$." . $defaultLanguage . "') $order");
                })
                ->orderColumn('category', function ($query, $order) use ($defaultLanguage) {
                    $query->join('blog_categories as bc', 'blogs.blog_category_id', '=', 'bc.id')
                          ->orderByRaw("JSON_EXTRACT(bc.name, '$." . $defaultLanguage . "') $order");
                })
                ->orderColumn('author', function ($query, $order) {
                    $query->join('users as u', 'blogs.user_id', '=', 'u.id')
                          ->orderBy('u.firstname', $order)
                          ->orderBy('u.lastname', $order);
                })
                ->orderColumn('views', function ($query, $order) {
                    $query->orderBy('views', $order);
                })
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('status', $order);
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('blogs.created_at', $order);
                })
                ->orderColumn('updated_at', function ($query, $order) {
                    $query->orderBy('blogs.updated_at', $order);
                })
                ->rawColumns(['title', 'category', 'author', 'views', 'status', 'created_at', 'updated_at', 'action'])
                ->make(true);
        }

        return view('themes.default.back.admins.pages.blogs.articles-list', compact('categories'));
    }

    public function show(Request $request)
    {
        if (!Gate::allows('show blog')) {
            return view('themes.default.back.permission-denied');
        }

        $id = decrypt($request->id);
        $blog = Blog::findOrFail($id);

        return view('themes.default.back.admins.pages.blogs.articles-show', compact('blog'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add blog')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:160',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:published,draft',
            'auto_translate' => 'nullable|in:on',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = upload_to_public($request->file('image'), 'images/blog');
        }

        $cachedData = app('cached_data');
        $defaultLanguage = $cachedData['settings']['default_language'];

        $keywordsData = json_decode($request->meta_keywords, true);
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
            $metakeywords = $request->meta_keywords;
        }

        $blog = Blog::create([
            'meta_keywords' => [$defaultLanguage => $metakeywords],
            'meta_description' => [$defaultLanguage => $request->meta_description],
            'slug' => Blog::createUniqueSlug($request->title),
            'title' => [$defaultLanguage => $request->title],
            'content' => [$defaultLanguage => $request->content],
            'blog_category_id' => $request->blog_category_id,
            'image' => $image,
            'user_id' => Auth::user()->id,
            'status' => $request->status,
        ]);

        if ($request->input('auto_translate')) {
            $request->merge(['id' => encrypt($blog->id)]);
            $this->autoTranslate($request);
        }

        // إرسال إشعارات فقط إذا كان المقال منشورًا
        if ($blog->status === 'published') {
            $this->sendNewBlogNotifications($blog);
        }

        return redirect()->back()->with('success', __('l.Article added successfully'));
    }

    /**
     * إرسال إشعارات للمشتركين النشطين عن البلوج الجديد
     *
     * @param \App\Models\Blog $blog
     * @return void
     */
    private function sendNewBlogNotifications($blog)
    {
        try {
            // جلب معرفات جميع المشتركين النشطين - فقط الIDs وليس الكائنات الكاملة لتوفير الذاكرة
            $subscriberIds = \App\Models\Subscriber::active()->pluck('id')->toArray();

            if (empty($subscriberIds)) {
                Log::info('No active subscribers found for blog notification');
                return;
            }

            // تقسيم المشتركين إلى مجموعات صغيرة (50 لكل مجموعة) لتجنب الضغط على الخادم
            $chunks = array_chunk($subscriberIds, 50);

            Log::info('Dispatching notifications for new blog post (ID: ' . $blog->id . ') to ' . count($subscriberIds) . ' subscribers in ' . count($chunks) . ' batches');

            // تأخير تدريجي لتوزيع العبء على مدار فترة زمنية
            $delayMinutes = 0;

            foreach ($chunks as $index => $chunk) {
                // إضافة تأخير 2 دقيقة لكل مجموعة بعد الأولى
                if ($index > 0) {
                    $delayMinutes += 2;
                }

                // إرسال المهمة إلى الطابور مع التأخير المحدد
                \App\Jobs\SendNewBlogNotificationJob::dispatch($blog, $chunk)
                    ->delay(now()->addMinutes($delayMinutes));

                Log::info('Batch ' . ($index + 1) . ' scheduled with ' . count($chunk) . ' subscribers, delay: ' . $delayMinutes . ' minutes');
            }
        } catch (\Exception $e) {
            Log::error('Error scheduling blog notifications: ' . $e->getMessage());
        }
    }

    public function getTranslations(Request $request)
    {
        if (!Gate::allows('edit blog')) {
            return view('themes.default.back.permission-denied');
        }

        $blog = Blog::findOrFail(decrypt($request->id));
        $languages = Language::where('is_active', 1)->get();

        return view('themes.default.back.admins.pages.blogs.articles-translations', compact('blog', 'languages'));
    }

    public function translate(Request $request)
    {
        if (!Gate::allows('edit blog')) {
            return view('themes.default.back.permission-denied');
        }

        // تحقق من جميع حقول الترجمة
        $validationRules = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'title-')) {
                $validationRules[$key] = 'required|string|max:255';
            }
            if (str_starts_with($key, 'content-')) {
                $validationRules[$key] = 'required|string';
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

        $blog = Blog::findOrFail(decrypt($request->id));
        $currentTitle = is_array($blog->title) ? $blog->title : [];
        $currentContent = is_array($blog->content) ? $blog->content : [];
        $currentMetaKeywords = is_array($blog->meta_keywords) ? $blog->meta_keywords : [];
        $currentMetaDescription = is_array($blog->meta_description) ? $blog->meta_description : [];

        $translationsTitle = [];
        $translationsContent = [];
        $translationsMetaKeywords = [];
        $translationsMetaDescription = [];

        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'title-')) {
                $languageCode = str_replace('title-', '', $key);
                $translationsTitle[$languageCode] = $value;
            }
            if (str_starts_with($key, 'content-')) {
                $languageCode = str_replace('content-', '', $key);
                $translationsContent[$languageCode] = $value;
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

        $blog->update([
            'title' => array_merge($currentTitle, $translationsTitle),
            'content' => array_merge($currentContent, $translationsContent),
            'meta_keywords' => array_merge($currentMetaKeywords, $translationsMetaKeywords),
            'meta_description' => array_merge($currentMetaDescription, $translationsMetaDescription),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    public function autoTranslate(Request $request)
    {
        if (!Gate::allows('edit blog')) {
            return view('themes.default.back.permission-denied');
        }

        $blog = Blog::findOrFail(decrypt($request->id));
        $currentTitle = is_array($blog->title) ? $blog->title : [];
        $currentContent = is_array($blog->content) ? $blog->content : [];
        $currentMetaKeywords = is_array($blog->meta_keywords) ? $blog->meta_keywords : [];
        $currentMetaDescription = is_array($blog->meta_description) ? $blog->meta_description : [];

        $cachedData = app('cached_data');
        $sourceLanguage = $cachedData['settings']['default_language'];

        $languages = Language::where('is_active', 1)
            ->where('code', '!=', $sourceLanguage)
            ->get();

        $translationsTitle = [];
        $translationsContent = [];
        $translationsMetaKeywords = [];
        $translationsMetaDescription = [];
        $translator = new GoogleTranslate();

        foreach ($languages as $language) {
            try {
                // ترجمة العنوان
                $translatedName = $translator
                    ->setSource($sourceLanguage)
                    ->setTarget($language->code)
                    ->translate($blog->getTranslation('title', $sourceLanguage));

                // ترجمة وصف الميتا إذا كان موجودًا
                if ($blog->meta_description) {
                    $sourceMetaDescription = $blog->getTranslation('meta_description', $sourceLanguage, false);
                    if (!empty($sourceMetaDescription)) {
                        $translatedMetaDescription = $translator
                            ->setSource($sourceLanguage)
                            ->setTarget($language->code)
                            ->translate($sourceMetaDescription);
                        $translationsMetaDescription[$language->code] = $translatedMetaDescription;
                    }
                }

                // ترجمة الكلمات المفتاحية إذا كانت موجودة
                if ($blog->getTranslation('meta_keywords', $sourceLanguage)) {
                    $sourceKeywords = $blog->getTranslation('meta_keywords', $sourceLanguage);

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

                // الحصول على المحتوى الأصلي
                $originalContent = $blog->getTranslation('content', $sourceLanguage);

                // إنشاء DOM Document
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML(mb_convert_encoding($originalContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();

                // استخراج النصوص وترجمتها
                $translatedContent = $this->translateDOMNode($dom->documentElement, $translator, $sourceLanguage, $language->code);

                $translationsTitle[$language->code] = $translatedName;
                $translationsContent[$language->code] = $translatedContent;

                // تأخير قصير لتجنب تجاوز حد طلبات Google Translate
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                Log::error('Translation error for language ' . $language->code . ': ' . $e->getMessage());
                continue;
            }
        }

        $blog->update([
            'title' => array_merge($currentTitle, $translationsTitle),
            'content' => array_merge($currentContent, $translationsContent),
            'meta_keywords' => array_merge($currentMetaKeywords, $translationsMetaKeywords),
            'meta_description' => array_merge($currentMetaDescription, $translationsMetaDescription),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    private function translateDOMNode($node, $translator, $sourceLanguage, $targetLanguage)
    {
        if ($node === null) {
            return '';
        }

        // إذا كان العنصر نصياً
        if ($node->nodeType === XML_TEXT_NODE) {
            $text = trim($node->nodeValue);
            if (!empty($text)) {
                try {
                    return $translator
                        ->setSource($sourceLanguage)
                        ->setTarget($targetLanguage)
                        ->translate($text);
                } catch (\Exception $e) {
                    Log::error('Translation error: ' . $e->getMessage());
                    return $text;
                }
            }
            return $text;
        }

        // إذا كان العنصر HTML
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $innerHTML = '';
            foreach ($node->childNodes as $child) {
                $translatedChild = $this->translateDOMNode($child, $translator, $sourceLanguage, $targetLanguage);
                $innerHTML .= $translatedChild;
            }

            // إعادة بناء العنصر مع النص المترجم
            $tag = $node->tagName;
            $attributes = '';

            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attr) {
                    $attributes .= ' ' . $attr->name . '="' . $attr->value . '"';
                }
            }

            // تجاهل ترجمة محتوى بعض العناصر
            $skipTranslationTags = ['code', 'pre', 'script', 'style'];
            if (in_array(strtolower($tag), $skipTranslationTags)) {
                return $node->ownerDocument->saveHTML($node);
            }

            return "<{$tag}{$attributes}>{$innerHTML}</{$tag}>";
        }

        return '';
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit blog')) {
            return view('themes.default.back.permission-denied');
        }

        $id = decrypt($request->id);
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::all();

        return view('themes.default.back.admins.pages.blogs.articles-edit', compact('blog', 'categories'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit blog')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:160',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:published,draft',
            'views' => 'required|integer',
        ]);

        $blog = Blog::findOrFail(decrypt($request->id));

        $currentTitle = is_array($blog->title) ? $blog->title : [];
        $currentContent = is_array($blog->content) ? $blog->content : [];
        $currentMetaKeywords = is_array($blog->meta_keywords) ? $blog->meta_keywords : [];
        $currentMetaDescription = is_array($blog->meta_description) ? $blog->meta_description : [];
        $defaultLanguage = Setting::where('option', 'default_language')->first()->value;

        $image = $blog->image;
        if ($request->hasFile('image')) {
            if ($blog->image) {
                $path = public_path($blog->image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $image = upload_to_public($request->file('image'), 'images/blog');
        }

        $keywordsData = json_decode($request->meta_keywords, true);
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
            $metakeywords = $request->meta_keywords;
        }

        $blog->update([
            'meta_keywords' => array_merge($currentMetaKeywords, [$defaultLanguage => $metakeywords]),
            'meta_description' => array_merge($currentMetaDescription, [$defaultLanguage => $request->meta_description]),
            'title' => array_merge($currentTitle, [$defaultLanguage => $request->title]),
            'content' => array_merge($currentContent, [$defaultLanguage => $request->content]),
            'blog_category_id' => $request->blog_category_id,
            'image' => $image,
            'status' => $request->status,
            'views' => $request->views,
            'slug' => Blog::createUniqueSlug($request->title, $blog->id),
        ]);

        return redirect()->back()->with('success', __('l.Article updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete blog')) {
            return view('themes.default.back.permission-denied');
        }

        $id = decrypt($request->id);
        $blog = Blog::findOrFail($id);

        if ($blog->image) {
            $path = public_path($blog->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $blog->delete();

        return redirect()->back()->with('success', __('l.Article deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete blog')) {
            return view('themes.default.back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        $articles = Blog::whereIn('id', $ids)->get();

        foreach($articles as $article) {
            if ($article->image) {
                $path = public_path($article->image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $article->delete();
        }

        return redirect()->back()->with('success', __('l.Articles deleted successfully'));
    }

    public function deleteComment(Request $request)
    {
        if (!Gate::allows('delete blog')) {
            return redirect()->back()->with('error', __('l.Permission denied'));
        }

        try {
            $comment = BlogComment::findOrFail(decrypt($request->id));
            $comment->delete(); // سيتم حذف جميع الردود تلقائياً بسبب onDelete('cascade')

            return redirect()->back()->with('success', __('l.Comment deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('l.Something went wrong!'));
        }
    }
}
