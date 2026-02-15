<?php

namespace App\Http\Controllers\Web\Back\Users\Payment;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\Test;
use App\Models\Invoice;
use App\Models\Course;
use App\Models\Live;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Nafezly\Payments\Classes\KashierPayment;

class PaymentController extends Controller
{
    /**
     * معالجة طلب الدفع
     */
    public function processPayment(Request $request)
    {
        // التحقق من نوع العنصر المراد شراؤه
        if ($request->has('lecture_id')) {
            return $this->processLecturePayment($request);
        } elseif ($request->has('test_id') || ($request->has('payment_type') && in_array($request->payment_type, ['single_test', 'course_tests']))) {
            return $this->processTestPayment($request);
        } elseif ($request->has('live_id')) {
            return $this->processLivePayment($request);
        }

        return redirect()->back()->with('error', 'نوع الدفع غير صحيح');
    }

    /**
     * التحقق من انتهاء صلاحية الوصول وإظهار رسالة تحذيرية
     */
    private function checkAccessExpiry($user, $courseId = null)
    {
        if (!$courseId) {
            return null;
        }

        $course = Course::find($courseId);
        if (!$course || !$course->hasAccessDurationLimit()) {
            return null;
        }

        // التحقق من انتهاء المدة
        if ($user->isCourseAccessExpired($courseId)) {
            $expiryDate = $user->getCourseAccessExpiryDate($courseId);
            return [
                'expired' => true,
                'message' => 'انتهت مدة الإتاحة لهذا الكورس. يجب إعادة الشراء للحصول على الوصول.',
                'expiry_date' => $expiryDate ? $expiryDate->format('Y-m-d') : null
            ];
        }

        // التحقق من اقتراب انتهاء المدة (آخر 3 أيام)
        $daysLeft = $user->getCourseAccessDaysLeft($courseId);

        if ($daysLeft !== null && $daysLeft <= 3 && $daysLeft > 0) {
            $expiryDate = $user->getCourseAccessExpiryDate($courseId);
            return [
                'expired' => false,
                'warning' => true,
                'message' => "ستنتهي مدة الإتاحة خلال {$daysLeft} أيام. تاريخ الانتهاء: " . ($expiryDate ? $expiryDate->format('Y-m-d') : 'غير محدد'),
                'days_left' => $daysLeft,
                'expiry_date' => $expiryDate ? $expiryDate->format('Y-m-d') : null
            ];
        }

        return null;
    }

    /**
     * إضافة معلومات انتهاء الصلاحية للعرض
     */
    private function addExpiryInfoToView($courseId, $viewData = [])
    {
        $user = auth()->user();
        $course = Course::find($courseId);

        if (!$course || !$course->hasAccessDurationLimit()) {
            return $viewData;
        }

        $expiryDate = $user->getCourseAccessExpiryDate($courseId);
        $daysLeft = $user->getCourseAccessDaysLeft($courseId);

        if ($expiryDate) {
            $viewData['access_expiry'] = [
                'expiry_date' => $expiryDate,
                'days_left' => $daysLeft,
                'formatted_duration' => $course->getFormattedAccessDuration(),
                'is_expired' => $user->isCourseAccessExpired($courseId),
                'is_warning' => $daysLeft !== null && $daysLeft <= 3 && $daysLeft > 0
            ];
        }

        return $viewData;
    }

    /**
     * معالجة دفع المحاضرات
     */
    private function processLecturePayment(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'payment_type' => 'required|in:single,course,month'
        ]);

        $user = auth()->user();
        $lecture = Lecture::with('course')->findOrFail($request->lecture_id);

        // التحقق من صحة نوع الدفع مع نوع المحاضرة
        if ($request->payment_type !== 'course' && !$this->validatePaymentType($lecture, $request->payment_type)) {
            return redirect()->back()->with('error', __('l.invalid_payment_type'));
        }

        // التحقق من عدم وجود دفع سابق
        if ($this->hasAlreadyPaid($user, $lecture, $request->payment_type)) {
            // التحقق من انتهاء صلاحية الوصول
            $expiryCheck = $this->checkAccessExpiry($user, $lecture->course_id);
            if ($expiryCheck && $expiryCheck['expired']) {
                // انتهت الصلاحية، يجب إعادة الشراء
                session()->flash('warning', $expiryCheck['message']);
            } else {
                return redirect()->route('dashboard.users.courses-lectures-show', ['id' => encrypt($lecture->id)])
                       ->with('info', __('l.already_purchased'));
            }
        // } else {
        //     // التحقق من اقتراب انتهاء الصلاحية للمحتوى المدفوع مسبقاً
        //     $expiryCheck = $this->checkAccessExpiry($user, $lecture->course_id);
        //     if ($expiryCheck && $expiryCheck['warning']) {
        //         session()->flash('warning', $expiryCheck['message']);
        //     }
        }

        try {
            // حساب قيمة المدفوع
            if ($request->payment_type === 'course') {
                // شراء كورس مباشر
                $amount = $lecture->course->price ?? 0;
            } else {
                $paymentInfo = $this->getPaymentInfo($lecture, $user);
                $amount = $paymentInfo['amount'];
            }

            // حساب رسوم بوابة الدفع
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            // إعداد بوابة الدفع Kashier
            $payment = new KashierPayment();
            $response = $payment
                ->setAmount(round($total, 2))
                ->setSource('card,bank_installments,wallet,fawry')
                ->pay();

            // إنشاء الفاتورة مع بيانات الدفع
            $invoice = $this->createInvoiceWithPayment($user, $lecture, $request->payment_type, $total, $response['payment_id'] ?? null);

            // توجيه المستخدم لصفحة الدفع
            return view('themes/default/back.users.payment.pay', ['link' => $response['html']]);

        } catch (\Exception $e) {
            logger('Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('l.payment_failed'));
        }
    }

    /**
     * معالجة دفع الاختبارات
     */
    private function processTestPayment(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:single_test,course_tests',
            'test_id' => 'required_if:payment_type,single_test|exists:tests,id',
            'course_id' => 'required_if:payment_type,course_tests|exists:courses,id'
        ]);

        $user = auth()->user();

        try {
            if ($request->payment_type === 'single_test') {
                // شراء اختبار مفرد
                $test = Test::with('course')->findOrFail($request->test_id);

                // التحقق من عدم وجود دفع سابق
                if ($user->canAccessTest($test->id)) {
                    // التحقق من انتهاء صلاحية الوصول
                    $expiryCheck = $this->checkAccessExpiry($user, $test->course_id);
                    if ($expiryCheck && $expiryCheck['expired']) {
                        // انتهت الصلاحية، يجب إعادة الشراء
                        session()->flash('warning', $expiryCheck['message']);
                    } else {
                        return redirect()->route('dashboard.users.tests.show', $test->id)
                            ->with('info', 'لقد اشتريت هذا الاختبار مسبقاً');
                    }
                // } else {
                //     // التحقق من اقتراب انتهاء الصلاحية
                //     $expiryCheck = $this->checkAccessExpiry($user, $test->course_id);
                //     if ($expiryCheck && $expiryCheck['warning']) {
                //         session()->flash('warning', $expiryCheck['message']);
                //     }
                }

                $amount = $test->price;
                $description = 'شراء اختبار: ' . $test->name;
                $courseId = $test->course_id;
                $typeValue = $test->id;

            } else {
                // شراء جميع اختبارات الكورس
                $course = Course::with('activeTests')->findOrFail($request->course_id);

                // التحقق من عدم وجود دفع سابق
                if ($user->hasPurchasedCourseQuizzes($course->id)) {
                    // التحقق من انتهاء صلاحية الوصول
                    $expiryCheck = $this->checkAccessExpiry($user, $course->id);
                    if ($expiryCheck && $expiryCheck['expired']) {
                        // انتهت الصلاحية، يجب إعادة الشراء
                        session()->flash('warning', $expiryCheck['message']);
                    } else {
                        return redirect()->route('dashboard.users.tests')
                            ->with('info', 'لقد اشتريت جميع اختبارات هذا الكورس مسبقاً');
                    }
                // } else {
                //     // التحقق من اقتراب انتهاء الصلاحية
                //     $expiryCheck = $this->checkAccessExpiry($user, $course->id);
                //     if ($expiryCheck && $expiryCheck['warning']) {
                //         session()->flash('warning', $expiryCheck['message']);
                //     }
                }

                $amount = $course->tests_price ?: $course->activeTests->sum('price');
                $description = 'شراء جميع اختبارات كورس: ' . $course->name;
                $courseId = $course->id;
                $typeValue = $course->id;
            }

            // حساب رسوم بوابة الدفع
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            // إعداد بوابة الدفع Kashier
            $payment = new KashierPayment();
            $response = $payment
                ->setAmount(round($total, 2))
                ->setSource('card,bank_installments,wallet,fawry')
                ->pay();

            // إنشاء الفاتورة مع بيانات الدفع
            $invoice = $this->createTestInvoiceWithPayment(
                $user,
                $request->payment_type,
                $courseId,
                $typeValue,
                $total,
                $description,
                $response['payment_id'] ?? null
            );

            // توجيه المستخدم لصفحة الدفع
            return view('themes/default/back.users.payment.pay', ['link' => $response['html']]);

        } catch (\Exception $e) {
            logger('Test Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'فشل في معالجة الدفع');
        }
    }

    /**
     * معالجة دفع البث المباشر (اللايفات)
     */
    private function processLivePayment(Request $request)
    {
        $request->validate([
            'live_id' => 'required|exists:lives,id'
        ]);

        $user = auth()->user();
        $live = Live::with('course')->findOrFail($request->live_id);

        // التحقق من نوع البث المباشر - يجب أن يكون مجاني أو مدفوع فقط
        if (!in_array($live->type, ['free', 'price'])) {
            return redirect()->back()->with('error', 'نوع البث المباشر غير مدعوم. يجب أن يكون مجاني أو مدفوع فقط.');
        }

        // إذا كان البث مجاني، توجيه المستخدم مباشرة
        if ($live->isFree()) {
            return redirect()->route('dashboard.users.lives.show', $live->id)
                   ->with('success', 'يمكنك الانضمام للبث المباشر مجاناً');
        }

        // التحقق من عدم وجود دفع سابق للبث المدفوع
        if ($user->hasPaidLive($live->id)) {
            // التحقق من انتهاء صلاحية الوصول
            $expiryCheck = $this->checkAccessExpiry($user, $live->course_id);
            if ($expiryCheck && $expiryCheck['expired']) {
                // انتهت الصلاحية، يجب إعادة الشراء
                session()->flash('warning', $expiryCheck['message']);
            } else {
                return redirect()->route('dashboard.users.lives.show', $live->id)
                       ->with('info', 'لقد اشتريت هذا البث المباشر مسبقاً');
            }
        // } else {
        //     // التحقق من اقتراب انتهاء الصلاحية
        //     $expiryCheck = $this->checkAccessExpiry($user, $live->course_id);
        //     if ($expiryCheck && $expiryCheck['warning']) {
        //         session()->flash('warning', $expiryCheck['message']);
        //     }
        }

        try {
            // حساب قيمة المدفوع للبث المدفوع
            $amount = $live->price;

            if ($amount <= 0) {
                return redirect()->back()->with('error', 'سعر البث المباشر غير صحيح');
            }

            // حساب رسوم بوابة الدفع
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            // إعداد بوابة الدفع Kashier
            $payment = new KashierPayment();
            $response = $payment
                ->setAmount(round($total, 2))
                ->setSource('card,bank_installments,wallet,fawry')
                ->pay();

            // إنشاء الفاتورة مع بيانات الدفع
            $invoice = $this->createLiveInvoiceWithPayment(
                $user,
                $live,
                $total,
                $response['payment_id'] ?? null
            );

            // توجيه المستخدم لصفحة الدفع
            return view('themes/default/back.users.payment.pay', ['link' => $response['html']]);

        } catch (\Exception $e) {
            logger('Live Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'فشل في معالجة الدفع');
        }
    }

    /**
     * التحقق من صحة نوع الدفع
     */
    private function validatePaymentType($lecture, $paymentType)
    {
        switch ($lecture->type) {
            case 'price':
                // للمحاضرات المدفوعة - دفع محاضرة واحدة
                return $paymentType === 'single';
            case 'course':
            case 'month':
                // للمحاضرات الشهرية والكورس - دفع الكورس بالكامل
                return $paymentType === 'course';
            default:
                return false;
        }
    }

    /**
     * التحقق من الدفع السابق
     */
    private function hasAlreadyPaid($user, $lecture, $paymentType)
    {
        switch ($paymentType) {
            case 'single':
                // التحقق من دفع المحاضرة المنفردة
                return $user->hasPaidLecture($lecture->id, $lecture->course_id);
            case 'course':
                // التحقق من شراء الكورس بالكامل (ينطبق على course و month)
                return $user->hasPurchasedCourseLectures($lecture->course_id);
            default:
                return false;
        }
    }

    /**
     * إنشاء الفاتورة مع بيانات الدفع
     */
    private function createInvoiceWithPayment($user, $lecture, $paymentType, $amount, $paymentId)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'lecture',
            'type' => $paymentType,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId
        ];

        // تحديد type_value حسب نوع الدفع
        switch ($paymentType) {
            case 'single':
                $invoiceData['type_value'] = $lecture->id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
            case 'course':
            case 'month':
                $invoiceData['type_value'] = $lecture->course_id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
        }

        return Invoice::create($invoiceData);
    }

    /**
     * إنشاء فاتورة للاختبارات مع بيانات الدفع
     */
    private function createTestInvoiceWithPayment($user, $paymentType, $courseId, $typeValue, $amount, $description, $paymentId)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'quiz',
            'type' => $paymentType === 'single_test' ? 'single' : 'course',
            'type_value' => $typeValue,
            'course_id' => $courseId,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId,
        ];

        return Invoice::create($invoiceData);
    }

    /**
     * إنشاء فاتورة للبث المباشر مع بيانات الدفع
     */
    private function createLiveInvoiceWithPayment($user, $live, $amount, $paymentId)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'live',
            'type' => 'single',
            'type_value' => $live->id,
            'course_id' => $live->course_id,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId,
        ];

        return Invoice::create($invoiceData);
    }

    /**
     * إنشاء الفاتورة (للدفع المباشر بدون بوابة)
     */
    private function createInvoice($user, $lecture, $paymentType, $amount)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'lecture',
            'type' => $paymentType,
            'amount' => $amount,
            'status' => 'pending'
        ];

        // تحديد type_value حسب نوع الدفع
        switch ($paymentType) {
            case 'single':
                $invoiceData['type_value'] = $lecture->id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
            case 'course':
                $invoiceData['type_value'] = $lecture->course_id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
        }

        return Invoice::create($invoiceData);
    }

    /**
     * تحديث حالة الفاتورة إلى مدفوع
     */
    private function markInvoiceAsPaid($invoice)
    {
        $invoice->update([
            'status' => 'paid',
        ]);
    }

    /**
     * حساب معلومات الدفع للمحاضرة
     */
    public function getPaymentInfo($lecture, $user)
    {
        switch ($lecture->type) {
            case 'price':
                // دفع محاضرة واحدة
                return [
                    'type' => 'single',
                    'amount' => $lecture->price,
                    'description' => __('l.single_lecture_payment'),
                    'course_id' => $lecture->course_id
                ];

            case 'course':
            case 'month':
                // دفع الكورس بالكامل (لكل من course و month)
                return [
                    'type' => 'course',
                    'amount' => $lecture->course->price ?? 0,
                    'description' => __('l.full_course_access'),
                    'course_id' => $lecture->course_id
                ];

            default:
                return [
                    'type' => 'unknown',
                    'amount' => 0,
                    'description' => __('l.unknown_payment_type')
                ];
        }
    }



    /**
     * التحقق من حالة الدفع
     */
    public function verify($payment, Request $request)
    {
        try {
            $payment = new KashierPayment();
            $response = $payment->verify($request);

            // جلب الفاتورة من قاعدة البيانات باستخدام معرف الدفع
            $invoice = Invoice::where('pid', $response['payment_id'])->first();

            if ($response['success'] == 'true' && $invoice) {
                if ($invoice->status != 'paid') {
                    $invoice->status = 'paid';
                    $invoice->save();
                }

                return redirect()->route('dashboard.users.payment-success', ['invoice_id' => $invoice->id])
                    ->with('success', __('l.payment_successful'));
            } else {
                if ($invoice) {
                    $invoice->status = 'failed';
                    $invoice->save();
                }

                return redirect()->route('dashboard.users.payment-failed', ['invoice_id' => $invoice->id ?? ''])
                    ->with('error', __('l.payment_failed'));
            }
        } catch (\Exception $e) {
            logger('Payment Verification Error: ' . $e->getMessage());
            return redirect()->route('dashboard.users.payment-failed')
                ->with('error', __('l.payment_failed'));
        }
    }

    /**
     * صفحة تأكيد الدفع
     */
    public function paymentSuccess(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = Invoice::with(['student', 'lecture', 'course'])
                         ->where('user_id', auth()->id())
                         ->findOrFail($invoiceId);

        return view('themes/default/back.users.payment.success', compact('invoice'));
    }

    /**
     * صفحة فشل الدفع
     */
    public function paymentFailed(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = Invoice::with(['student', 'lecture', 'course'])
                         ->where('user_id', auth()->id())
                         ->findOrFail($invoiceId);

        return view('themes/default/back.users.payment.failed', compact('invoice'));
    }

    /**
     * صفحة إلغاء الدفع
     */
    public function paymentCancelled(Request $request)
    {
        return view('themes/default/back.users.payment.cancelled');
    }


}