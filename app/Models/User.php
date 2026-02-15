<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, AuthenticationLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function isOnline()
    {
        return DB::table('sessions')
            ->where('user_id', $this->id) // معرف المستخدم الحالي
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp) // خلال آخر 5 دقائق
            ->exists();
    }

    public function lastActivity()
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->first();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * العلاقة مع المحادثات التي يشارك فيها المستخدم
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_participants')
            ->withPivot(['is_admin', 'last_read_at'])
            ->withTimestamps();
    }

    /**
     * العلاقة مع المحادثات التي أنشأها المستخدم
     */
    public function createdChats()
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    /**
     * العلاقة مع رسائل المحادثة التي أرسلها المستخدم
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * العلاقة مع المستوى
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * العلاقة مع الكورسات المشتراة (عبر الاشتراك الشهري أو شراء الكورس كاملاً)
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'invoices', 'user_id', 'course_id')
            ->where(function($query) {
                $query->where('type', 'course')
                      ->orWhere('type', 'month');
            })
            ->wherePivot('status', 'paid')
            ->withPivot(['amount', 'status', 'created_at', 'type', 'type_value']);
    }

    /**
     * التحقق من شراء محاضرات كورس معين بالكامل
     */
    public function hasPurchasedCourseLectures($courseId)
    {
        return $this->invoices()
            ->where('category', 'lecture')
            ->where('type', 'course')
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->exists();
    }
    /**
     * التحقق من شراء اختبارات كورس معين بالكامل
     */
    public function hasPurchasedCourseQuizzes($courseId)
    {
        return $this->invoices()
            ->where('category', 'quiz')
            ->where('type', 'course')
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->exists();
    }

    /**
     * التحقق من دفع محاضرة معينة
     */
    public function hasPaidLecture($lectureId, $courseId = null)
    {
        $query = $this->invoices()
            ->where('category', 'lecture')
            ->where('type', 'single')
            ->where('type_value', $lectureId)
            ->where('status', 'paid');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->exists();
    }

    /**
     * التحقق من دفع اختبار معين
     */
    public function hasPaidQuiz($quizId, $courseId = null)
    {
        $query = $this->invoices()
            ->where('category', 'quiz')
            ->where('type', 'single')
            ->where('type_value', $quizId)
            ->where('status', 'paid');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->exists();
    }

    /**
     * التحقق من دفع شهر معين لكورس معين (format: YYYY-MM)
     */
    public function hasPaidMonth($monthYear, $courseId = null)
    {
        $query = $this->invoices()
            ->where('type', 'month')
            ->where('type_value', $monthYear)
            ->where('status', 'paid');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->exists();
    }

    /**
     * التحقق من دفع الشهر الحالي لكورس معين
     */
    public function hasPaidCurrentMonth($courseId = null)
    {
        $currentMonth = now()->format('Y-m');
        return $this->hasPaidMonth($currentMonth, $courseId);
    }

    /**
     * التحقق من إمكانية الوصول للمحاضرات الشهرية لكورس معين
     */
    public function canAccessMonthlyLectures($courseId = null)
    {
        $hasPaid = $this->hasPaidCurrentMonth($courseId);

        // إذا دفع وتم تحديد الكورس، تحقق من انتهاء صلاحية الوصول
        if ($hasPaid && $courseId && $this->isCourseAccessExpired($courseId)) {
            return false;
        }

        return $hasPaid;
    }

        /**
     * التحقق من إمكانية الوصول لمحاضرة معينة (جميع الطرق)
     */
    public function canAccessLecture($lectureId, $courseId = null)
    {
        // الحصول على بيانات المحاضرة
        $lecture = \App\Models\Lecture::find($lectureId);
        if (!$lecture) {
            return false;
        }

        // إذا لم يتم تمرير courseId، استخدم course_id من المحاضرة
        if (!$courseId) {
            $courseId = $lecture->course_id;
        }

        switch ($lecture->type) {
            case 'free':
                // محاضرة مجانية
                return true;

            case 'price':
                // محاضرة مدفوعة - تحتاج دفع منفصل أو شراء الكورس
                $hasPaid = $this->hasPaidLecture($lectureId, $courseId) || $this->hasPurchasedCourseLectures($courseId);

                // إذا دفع، تحقق من انتهاء صلاحية الوصول
                if ($hasPaid && $this->isCourseAccessExpired($courseId)) {
                    return false;
                }

                return $hasPaid;

            case 'month':
            case 'course':
                // محاضرة شهرية أو كورس - تحتاج شراء الكورس كاملاً
                $hasPurchased = $this->hasPurchasedCourseLectures($courseId);

                // إذا اشترى، تحقق من انتهاء صلاحية الوصول
                if ($hasPurchased && $this->isCourseAccessExpired($courseId)) {
                    return false;
                }

                return $hasPurchased;

            default:
                return false;
        }
    }

    /**
     * التحقق من إمكانية الوصول لواجب معين
     */
    public function canAccessAssignment($assignmentId)
    {
        // الحصول على بيانات الواجب والمحاضرة المرتبطة
        $assignment = \App\Models\LectureAssignment::with('lecture')->find($assignmentId);
        if (!$assignment || !$assignment->lecture) {
            return false;
        }

        // التحقق من الوصول للمحاضرة المرتبطة بالواجب
        return $this->canAccessLecture($assignment->lecture->id, $assignment->lecture->course_id);
    }

    /**
     * التحقق من إمكانية الوصول لاختبار معين
     */
    public function canAccessTest($testId, $courseId = null)
    {
        // الحصول على بيانات الاختبار
        $test = \App\Models\Test::find($testId);
        if (!$test) {
            return false;
        }

        // إذا لم يتم تمرير courseId، استخدم course_id من الاختبار
        if (!$courseId) {
            $courseId = $test->course_id;
        }

        // التحقق من شراء الاختبار المنفرد أو شراء كامل اختبارات الكورس
        $hasAccess = $this->hasPaidQuiz($testId, $courseId) || $this->hasPurchasedCourseQuizzes($courseId);

        // إذا كان لديه وصول، تحقق من انتهاء صلاحية الوصول
        if ($hasAccess && $this->isCourseAccessExpired($courseId)) {
            return false;
        }

        return $hasAccess;
    }

    /**
     * التحقق من دفع اشتراك شهري لكورس معين في أي وقت
     */
    public function hasPaidCourseMonthlySubscription($courseId)
    {
        $currentMonth = now()->format('Y-m');
        return $this->invoices()
            ->where('type', 'month')
            ->where('course_id', $courseId)
            ->where('type_value', $currentMonth)
            ->where('status', 'paid')
            ->exists();
    }

    /**
     * العلاقة مع الفواتير
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * الحصول على المحاضرات المدفوعة
     */
    public function paidLectures()
    {
        return $this->invoices()
            ->where('category', 'lecture')
            ->where('type', 'single')
            ->where('status', 'paid')
            ->pluck('type_value');
    }

    /**
     * الحصول على الاختبارات المدفوعة
     */
    public function paidQuizzes()
    {
        return $this->invoices()
            ->where('category', 'quiz')
            ->where('type', 'single')
            ->where('status', 'paid')
            ->pluck('type_value');
    }

    /**
     * الحصول على الأشهر المدفوعة (مع معرف الكورس اختياري)
     */
    public function paidMonths($courseId = null)
    {
        $query = $this->invoices()
            ->where('type', 'month')
            ->where('status', 'paid');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->pluck('type_value');
    }

    public function hasPaidLive($liveId, $courseId = null)
    {
        return $this->invoices()
            ->where('category', 'live')
            ->where('type', 'single')
            ->where('type_value', $liveId)
            ->where('status', 'paid')
            ->exists();
    }

    public function canAccessLive($liveId, $courseId = null)
    {
        $live = Live::find($liveId);
        if (!$live) {
            return false;
        }

        // إذا لم يتم تمرير courseId، استخدم course_id من البث المباشر
        if (!$courseId) {
            $courseId = $live->course_id;
        }

        switch ($live->type) {
            case 'free':
                return true;
            case 'price':
                $hasPaid = $this->hasPaidLive($liveId, $courseId);

                // إذا دفع، تحقق من انتهاء صلاحية الوصول
                if ($hasPaid && $this->isCourseAccessExpired($courseId)) {
                    return false;
                }

                return $hasPaid;
        }

        return false;
    }

    /**
     * الحصول على الكورسات التي لها اشتراك شهري مدفوع
     */
    public function paidMonthlyCourses()
    {
        return $this->invoices()
            ->where('type', 'month')
            ->where('status', 'paid')
            ->with('course')
            ->get()
            ->groupBy('course_id')
            ->map(function($invoices) {
                return [
                    'course' => $invoices->first()->course,
                    'months' => $invoices->pluck('type_value')->toArray(),
                    'latest_payment' => $invoices->max('created_at')
                ];
            });
    }

    /**
     * الحصول على إجمالي المبلغ المدفوع
     */
    public function getTotalPaidAmount()
    {
        return $this->invoices()
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * الحصول على إجمالي المبلغ المدفوع لكورس معين
     */
    public function getTotalPaidAmountForCourse($courseId)
    {
        return $this->invoices()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * الحصول على الفواتير المعلقة
     */
    public function getPaidInvoices()
    {
        return $this->invoices()
            ->where('status', 'paid')
            ->get();
    }

    /**
     * الحصول على المحادثات غير المقروءة
     */
    public function unreadChats()
    {
        return $this->chats()
            ->whereHas('messages', function ($query) {
                $query->whereColumn('chat_messages.created_at', '>', 'chat_participants.last_read_at')
                    ->where('chat_messages.user_id', '!=', $this->id);
            })
            ->orWhereHas('messages', function ($query) {
                $query->whereNull('chat_participants.last_read_at')
                    ->where('chat_messages.user_id', '!=', $this->id);
            });
    }


    public function canImpersonate()
    {
        if (!Gate::allows('show users')) {
            return false;
        }
        return true;
    }

    public function canBeImpersonated()
    {
        return true;
    }

    public function getPhotoAttribute()
    {
        //use auth()->user()->photo not auth()->user()->image

        if (isset($this->attributes['image']) && $this->attributes['image'] !== null && file_exists(public_path($this->attributes['image']))) {
            return asset($this->attributes['image']);
        } else {

            return asset('images/usersProfile/profile.png');

            // if (auth()->user()->gender == 'male') {
            //     return url('assets/img/team-3.jpg');
            // }
            // elseif (auth()->user()->gender == 'female') {
            //     return url('assets/img/team-1.jpg');
            // }
        }
    }

    const PROTECTED_EMAILS = [
        'root@admin.com',
        'admin@admin.com'
    ];

    /**
     * Get all student tests for this user
     */
    public function studentTests()
    {
        return $this->hasMany(StudentTest::class, 'student_id');
    }

    /**
     * Get completed tests for this user
     */
    public function completedTests()
    {
        return $this->hasMany(StudentTest::class, 'student_id')->where('status', 'completed');
    }

    /**
     * Get in-progress tests for this user
     */
    public function inProgressTests()
    {
        return $this->hasMany(StudentTest::class, 'student_id')
            ->whereIn('status', ['part1_in_progress', 'break_time', 'part2_in_progress']);
    }

    /**
     * Check if user has paid for a specific test
     */
    public function hasPaidTest($testId)
    {
        $test = Test::find($testId);
        if (!$test) {
            return false;
        }

        // Check if user has purchased the individual test (using 'quiz' category)
        $hasPaidIndividual = $this->invoices()
            ->where('category', 'quiz')
            ->where('type', 'single')
            ->where('type_value', $testId)
            ->where('status', 'paid')
            ->exists();

        if ($hasPaidIndividual) {
            // التحقق من انتهاء صلاحية الوصول
            if ($this->isCourseAccessExpired($test->course_id)) {
                return false;
            }
            return true;
        }

        // Check if user has purchased all tests for the course
        if ($test->course->tests_price) {
            // Check if user has paid for course tests using course type
            $hasPaidCourseTests = $this->invoices()
                ->where('category', 'quiz')
                ->where('type', 'course')
                ->where('course_id', $test->course_id)
                ->where('status', 'paid')
                ->exists();

            if ($hasPaidCourseTests) {
                // التحقق من انتهاء صلاحية الوصول
                if ($this->isCourseAccessExpired($test->course_id)) {
                    return false;
                }
                return true;
            }
        }

        return false;
    }


    /**
     * Get student test attempt for a specific test
     */
    public function getTestAttempt($testId)
    {
        return $this->studentTests()->where('test_id', $testId)->first();
    }

    /**
     * التحقق من انتهاء صلاحية الوصول للفاتورة
     */
    public function isInvoiceAccessExpired($invoice)
    {
        if (!$invoice || $invoice->status !== 'paid') {
            return true;
        }

        $course = $invoice->course;
        if (!$course || !$course->hasAccessDurationLimit()) {
            return false; // وصول غير محدود
        }

        $purchaseDate = $invoice->created_at;
        $expiryDate = $purchaseDate->copy()->addDays($course->getAccessDurationDays());

        return now()->gt($expiryDate);
    }

    /**
     * التحقق من انتهاء صلاحية الوصول لكورس معين
     */
    public function isCourseAccessExpired($courseId)
    {
        // البحث عن أحدث فاتورة مدفوعة لهذا الكورس
        $latestInvoice = $this->invoices()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestInvoice) {
            return true; // لا يوجد فاتورة مدفوعة
        }

        return $this->isInvoiceAccessExpired($latestInvoice);
    }

    /**
     * الحصول على تاريخ انتهاء صلاحية الوصول لكورس معين
     */
    public function getCourseAccessExpiryDate($courseId)
    {
        $latestInvoice = $this->invoices()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestInvoice) {
            return null;
        }

        $course = $latestInvoice->course;
        if (!$course || !$course->hasAccessDurationLimit()) {
            return null; // وصول غير محدود
        }

        return $latestInvoice->created_at->copy()->addDays($course->getAccessDurationDays());
    }

    /**
     * الحصول على عدد الأيام المتبقية للوصول لكورس معين
     */
    public function getCourseAccessDaysLeft($courseId)
    {
        $expiryDate = $this->getCourseAccessExpiryDate($courseId);

        if (!$expiryDate) {
            return null; // وصول غير محدود
        }

        $daysLeft = now()->diffInDays($expiryDate, false);
        return $daysLeft > 0 ? $daysLeft : 0;
    }
}
