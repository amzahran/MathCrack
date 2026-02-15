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

class FrontController extends Controller
{
public function index(Request $request)
{
    $seoPage = SeoPage::getBySlug('/');
    
    $totalUsers = DB::table('users')->count();
    $totalCourses = DB::table('courses')->count();
    $totalTests = DB::table('tests')->count();
    
    // افتراض أن معظم المستخدمين طلاب وعدد قليل مدرسين
    $instructors = 1;
    $students = $totalUsers - $instructors;
    
    $stats = [
        'satisfied_students' => $students,           // 9 طلاب
        'total_courses' => $totalCourses,            // 3 كورسات
        'Practice_Tests' => $totalTests,             // عدد اختبارات التدريب
        'expert_instructors' => $instructors,        // 1 مدرب
    ];

    return view(theme('front.index'), compact('seoPage', 'stats'));
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

        // فلترة حسب التصنيف
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
        $post = Blog::where('slug', $slug)->first();
        $related_posts = Blog::where('blog_category_id', $post->blog_category_id)->where('id', '!=', $post->id)->orderByDesc('id')->limit(3)->get();
        return view(theme('front.blog_details'), compact('post', 'related_posts', 'seoPage'));
    }

    public function blogReply(Request $request)
    {
        // التحقق من تسجيل دخول المستخدم
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // التحقق من صحة البيانات
        $request->validate([
            'content' => 'required|string',
            'blog_id' => 'required|exists:blogs,id',
            'parent_id' => 'nullable|exists:blog_comments,id'
        ]);

        // إنشاء تعليق جديد
        $comment = new BlogComment();
        $comment->blog_id = $request->blog_id;
        $comment->user_id = auth()->user()->id;
        $comment->content = $request->content;

        // إذا كان هناك parent_id فهو رد على تعليق
        if ($request->parent_id) {
            $comment->parent_id = $request->parent_id;
        }

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
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'phone' => 'required|string',
            'details' => 'required|string',
            'g-recaptcha-response' => 'nullable|string',
        ]);

        // التحقق من وجود حقل fax_number فخ البوتات
        if ($request->filled('fax_number')) {
            abort(403, 'Bot detected');
        }
        
        // جلب الإعداد من قاعدة البيانات
        $recaptchaEnabled = Setting::where('option', 'recaptcha')->first()->value ?? 0;

        // تحقق من reCAPTCHA إذا كانت مفعّلة
        if ($recaptchaEnabled == 1) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (empty($recaptchaResponse)) {
                return redirect()->back()->with('error', __('reCAPTCHA is required'));
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha.secret'),
                'response' => $recaptchaResponse,
                'remoteip' => IpUtils::anonymize($request->ip()),
            ]);

            $result = $response->json();

            if (!$response->successful() || empty($result['success']) || $result['success'] !== true) {
                return redirect()->back()->with('error', __('reCAPTCHA Error'));
            }
        }

        // تحقق من التكرار بناءً على IP
        $search = Contact::where('ip', $request->ip())->orderByDesc('id')->first();
        if ($search && strtotime($search->created_at . " +60 minutes") > time()) {
            return redirect()->back()->with('error', __('l.Error can not send more than 1 message every hour'));
        }

        // حفظ البيانات
        $contact = Contact::create([
            'ip' => $request->ip(),
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'phone' => $request->phone,
            'details' => $request->details,
            'status' => 0,
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
        $subscriber->email = $request->input('email');
        $subscriber->is_active = 1;
        $subscriber->unsubscribe_token = Str::random(32);
        $subscriber->save();

        return redirect()->back()->with('success', __('l.Subscriber added successfully'));
    }

    public function unsubscribe(Request $request, $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        if($subscriber){
            $subscriber->is_active = 0;
            $subscriber->save();
        }

        return redirect()->route('index')->with('success', __('l.Subscriber unsubscribed successfully'));
    }

    public function licenseVerify(Request $request)
    {
        $request->validate([
            'license_code'=> 'required',
        ]);

        $key = $request->input('license_code');

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
        $data_obj = $json_data['data'];
        $event = $json_data['event'];
        sort($data_obj['signatureKeys']);
        $headers = getallheaders();
        // Lower case all keys
        $headers = array_change_key_case($headers);
        $kashierSignature = $headers['x-kashier-signature'];
        $data = [];
        foreach ($data_obj['signatureKeys'] as $key) {
            $data[$key] = $data_obj[$key];
        }

        $paymentApiKey = config('nafezly-payments.KASHIER_IFRAME_KEY');
        $queryString = http_build_query($data, $numeric_prefix = "", $arg_separator = '&', $encoding_type = PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha256',$queryString, $paymentApiKey, false);;
        if ($signature == $kashierSignature) {
            if($data_obj['status'] == 'SUCCESS'){

                // $invoice = \App\Models\Invoice::where('pid', $data_obj['merchantOrderId'])->first();

                // if ($invoice->status != 'paid') {
                //     $invoice->status = 'paid';
                //     $invoice->save();
                // }

            }

            echo 'valid signature';
            http_response_code(200);
        } else {
            \App\Models\Setting::createOrUpdate([
                'option' => 'kashier_webhook',
                'value' => '0'
            ]);

           echo 'invalid signature';
           die();
        }
    }
    
}
