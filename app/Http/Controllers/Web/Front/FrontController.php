<?php

namespace App\Http\Controllers\Web\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Models\BlogComment;
use App\Models\Team;
use App\Models\Question;
use App\Models\Contact;
use App\Models\SeoPage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\Level;
use App\Models\Course;
use App\Models\Test;
use App\Models\Lecture;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        $seoPage = SeoPage::getBySlug('/');

        $stats = $this->getFrontStats();

        return view(theme('front.index'), compact('seoPage', 'stats'));
    }

    private function getFrontStats(): array
    {
        $totalLevels   = Level::count();
        $totalCourses  = Course::count();
        $totalLectures = Lecture::count();
        $totalTests    = Test::count();

        $totalUsers = Schema::hasTable('users') ? (int) DB::table('users')->count() : 0;

        $instructors = $this->countInstructors($totalUsers);
        $students    = $this->countStudents($totalUsers, $instructors);

        return [
            'satisfied_students' => $students,
            'Practice_Tests'     => $totalTests,
            'total_courses'      => $totalCourses,
            'expert_instructors' => $instructors,

            'levels'   => $totalLevels,
            'lectures' => $totalLectures,
            'tests'    => $totalTests,
            'courses'  => $totalCourses,
        ];
    }

    private function countInstructors(int $totalUsers): int
    {
        if ($totalUsers <= 0) {
            return 0;
        }

        // Spatie roles
        if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
            $roleNames = ['instructor', 'teacher', 'admin', 'super-admin'];

            $count = (int) DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->whereIn('roles.name', $roleNames)
                ->distinct('model_has_roles.model_id')
                ->count('model_has_roles.model_id');

            return max($count, 1);
        }

        // users.role
        if (Schema::hasColumn('users', 'role')) {
            $count = (int) DB::table('users')
                ->whereIn('role', ['instructor', 'teacher', 'admin'])
                ->count();

            return max($count, 1);
        }

        // users.type
        if (Schema::hasColumn('users', 'type')) {
            $count = (int) DB::table('users')
                ->whereIn('type', ['instructor', 'teacher', 'admin'])
                ->count();

            return max($count, 1);
        }

        // fallback
        return 1;
    }

    private function countStudents(int $totalUsers, int $instructors): int
    {
        if ($totalUsers <= 0) {
            return 0;
        }

        // Spatie roles
        if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
            $roleNames = ['student', 'learner'];

            $count = (int) DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->whereIn('roles.name', $roleNames)
                ->distinct('model_has_roles.model_id')
                ->count('model_has_roles.model_id');

            if ($count > 0) {
                return $count;
            }
        }

        // users.role
        if (Schema::hasColumn('users', 'role')) {
            $count = (int) DB::table('users')
                ->whereIn('role', ['student', 'learner'])
                ->count();

            if ($count > 0) {
                return $count;
            }
        }

        // users.type
        if (Schema::hasColumn('users', 'type')) {
            $count = (int) DB::table('users')
                ->whereIn('type', ['student', 'learner'])
                ->count();

            if ($count > 0) {
                return $count;
            }
        }

        // fallback
        return max($totalUsers - $instructors, 0);
    }

    public function about(Request $request)
    {
        $seoPage = SeoPage::getBySlug('about');
        return view(theme('front.about'), compact('seoPage'));
    }

    public function blog(Request $request)
    {
        $seoPage = SeoPage::getBySlug('blog');
        $query = Blog::orderByDesc('id');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $posts = $query->paginate(5);
        $popular_posts = Blog::orderByRaw('RAND()')->limit(3)->get();
        $categories = BlogCategory::whereHas('blogs')->get();

        return view(theme('front.blog'), compact('posts', 'categories', 'popular_posts', 'seoPage'));
    }

    public function blogDetails(Request $request, $slug)
    {
        $seoPage = SeoPage::getBySlug('blog-post');

        $post = Blog::where('slug', $slug)->firstOrFail();

        $related_posts = Blog::where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view(theme('front.blog_details'), compact('post', 'related_posts', 'seoPage'));
    }

    public function blogReply(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'content'   => 'required|string',
            'blog_id'   => 'required|exists:blogs,id',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $comment = new BlogComment();
        $comment->blog_id  = (int) $request->blog_id;
        $comment->user_id  = (int) auth()->id();
        $comment->content  = (string) $request->content;
        $comment->parent_id = $request->parent_id ? (int) $request->parent_id : null;
        $comment->save();

        return back()->with('success', __('l.Comment added successfully'));
    }

    public function team(Request $request)
    {
        $seoPage = SeoPage::getBySlug('team');
        $team_members = Team::all();
        return view(theme('front.team'), compact('team_members', 'seoPage'));
    }

    public function faqs(Request $request)
    {
        $seoPage = SeoPage::getBySlug('faq');
        $questions = Question::all();
        return view(theme('front.questions'), compact('questions', 'seoPage'));
    }

    public function contact(Request $request)
    {
        $seoPage = SeoPage::getBySlug('contact');
        return view(theme('front.contact'), compact('seoPage'));
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'subject'              => 'nullable|string|max:255',
            'phone'                => 'required|string|max:50',
            'details'              => 'required|string',
            'g-recaptcha-response' => 'nullable|string',
        ]);

        // honeypot
        if ($request->filled('fax_number')) {
            abort(403, 'Bot detected');
        }

        $recaptchaEnabled = (int) (Setting::where('option', 'recaptcha')->value('value') ?? 0);

        if ($recaptchaEnabled === 1) {
            $recaptchaResponse = (string) $request->input('g-recaptcha-response', '');

            if ($recaptchaResponse === '') {
                return redirect()->back()->with('error', __('reCAPTCHA is required'))->withInput();
            }

            $secret = config('app.recaptcha.secret');

            if (!$secret) {
                return redirect()->back()->with('error', __('reCAPTCHA Error'))->withInput();
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $recaptchaResponse,
                'remoteip' => IpUtils::anonymize($request->ip()),
            ]);

            $result = $response->json();

            if (
                !$response->successful() ||
                empty($result['success']) ||
                $result['success'] !== true
            ) {
                return redirect()->back()->with('error', __('reCAPTCHA Error'))->withInput();
            }
        }

        // rate limit by ip
        $last = Contact::where('ip', $request->ip())->orderByDesc('id')->first();
        if ($last && strtotime($last->created_at . ' +60 minutes') > time()) {
            return redirect()->back()->with('error', __('l.Error can not send more than 1 message every hour'))->withInput();
        }

        $contact = Contact::create([
            'ip'      => $request->ip(),
            'name'    => (string) $request->name,
            'email'   => (string) $request->email,
            'subject' => (string) $request->subject,
            'phone'   => (string) $request->phone,
            'details' => (string) $request->details,
            'status'  => 0,
        ]);

        \App\Jobs\ContactsJob::dispatch($contact);

        return redirect()->back()->with('success', __('l.Message sent successfully'));
    }

    public function terms(Request $request)
    {
        $seoPage = SeoPage::getBySlug('terms');
        return view(theme('front.terms'), compact('seoPage'));
    }

    public function privacy(Request $request)
    {
        $seoPage = SeoPage::getBySlug('privacy');
        return view(theme('front.privacy'), compact('seoPage'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = new Subscriber();
        $subscriber->email = (string) $request->input('email');
        $subscriber->is_active = 1;
        $subscriber->unsubscribe_token = Str::random(32);
        $subscriber->save();

        return redirect()->back()->with('success', __('l.Subscriber added successfully'));
    }

    public function unsubscribe(Request $request, $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if ($subscriber) {
            $subscriber->is_active = 0;
            $subscriber->save();
        }

        return redirect()->route('index')->with('success', __('l.Subscriber unsubscribed successfully'));
    }

    public function licenseVerify(Request $request)
    {
        $request->validate([
            'license_code' => 'required|string',
        ]);

        $key = (string) $request->input('license_code');

        DB::table('settings')->updateOrInsert(
            ['option' => 'key'],
            ['value' => $key]
        );

        return redirect()->back()->with('success', __('l.License updated successfully'));
    }

    public function kashierWebhook(Request $request)
    {
        $raw_payload = file_get_contents('php://input');
        $json_data = json_decode($raw_payload, true);

        $data_obj = $json_data['data'] ?? [];
        $signatureKeys = $data_obj['signatureKeys'] ?? [];

        if (!is_array($signatureKeys)) {
            $signatureKeys = [];
        }

        sort($signatureKeys);

        $headers = array_change_key_case(getallheaders());
        $kashierSignature = $headers['x-kashier-signature'] ?? '';

        $data = [];
        foreach ($signatureKeys as $key) {
            if (array_key_exists($key, $data_obj)) {
                $data[$key] = $data_obj[$key];
            }
        }

        $paymentApiKey = config('nafezly-payments.KASHIER_IFRAME_KEY');
        $queryString = http_build_query($data, "", '&', PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha256', $queryString, (string) $paymentApiKey, false);

        if ($kashierSignature !== '' && hash_equals($signature, $kashierSignature)) {
            echo 'valid signature';
            http_response_code(200);
            return;
        }

        Setting::createOrUpdate([
            'option' => 'kashier_webhook',
            'value'  => '0',
        ]);

        echo 'invalid signature';
        http_response_code(400);
        return;
    }
}
