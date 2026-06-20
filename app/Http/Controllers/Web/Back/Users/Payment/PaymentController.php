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
            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $sourceMethods = 'card,bank_installments,wallet,fawry';
            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            $payment = new KashierPayment();
            $response = $payment
                ->setAmount(round($total, 2))
                ->setSource($sourceMethods)
                ->pay();

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

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
            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $sourceMethods = 'card,bank_installments,wallet,fawry';
            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            $payment = new KashierPayment();
            $response = $payment
                ->setAmount(round($total, 2))
                ->setSource($sourceMethods)
                ->pay();

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

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
            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $sourceMethods = 'card,bank_installments,wallet,fawry';
            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            $payment = new KashierPayment();
            $response = $payment
                ->setAmount(round($total, 2))
                ->setSource($sourceMethods)
                ->pay();

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

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
        $invoice = $this->resolveInvoiceFromGatewayReference($request);

        try {
            logger('Kashier payment verification started', [
                'route_payment_parameter' => $payment,
                'request_keys' => array_keys($request->all()),
                'request_data' => $this->sanitizePaymentLogData($request->all()),
            ]);

            $kashierConfigured = $this->configureKashierGatewayFromDatabase();

            logger('Kashier payment verification configuration result', [
                'configured_from_database' => $kashierConfigured,
            ]);

            if (!$kashierConfigured) {
                if ($invoice?->status === 'paid') {
                    return $this->redirectToPaymentSuccess($invoice);
                }

                return $this->redirectToPaymentFailed($invoice, $request)
                    ->with('error', 'Payment gateway is not available. Please contact support.');
            }

            $payment = new KashierPayment();
            $verificationRequest = $request->duplicate();
            $callbackStatus = strtoupper(trim((string) $request->input('paymentStatus', '')));
            $gatewayReference = $this->gatewayReference($request->all());

            if (in_array($callbackStatus, ['SUCCESS', 'CAPTURED', 'PAID', 'APPROVED'], true)) {
                // The package only recognizes SUCCESS, then confirms CAPTURED via Kashier's API.
                $verificationRequest->merge(['paymentStatus' => 'SUCCESS']);
            }

            if (!$verificationRequest->filled('merchantOrderId') && $gatewayReference !== null) {
                $verificationRequest->merge(['merchantOrderId' => $gatewayReference]);
            }

            $response = $payment->verify($verificationRequest);

            logger('Kashier payment verification response', [
                'response' => $this->sanitizePaymentLogData($response),
                'success_value' => $response['success'] ?? null,
                'success_type' => isset($response['success']) ? gettype($response['success']) : null,
                'payment_id' => $response['payment_id'] ?? null,
            ]);

            // جلب الفاتورة من قاعدة البيانات باستخدام معرف الدفع
            $invoice = $this->resolveInvoiceFromGatewayReference($request, $response) ?? $invoice;

            logger('Kashier payment verification invoice lookup', [
                'payment_id' => $response['payment_id'] ?? null,
                'invoice_found' => (bool) $invoice,
                'invoice_id' => $invoice?->id,
                'invoice_status' => $invoice?->status,
            ]);

            $verified = filter_var($response['success'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // A signed webhook may finish before the browser returns from Kashier.
            if ($invoice?->status === 'paid') {
                return $this->redirectToPaymentSuccess($invoice);
            }

            if ($verified && $invoice) {
                if ($invoice->status != 'paid') {
                    $invoice->status = 'paid';
                    $invoice->save();
                }

                return $this->redirectToPaymentSuccess($invoice);
            } else {
                if ($verified && !$invoice) {
                    logger()->warning('Kashier verification succeeded without matching invoice', [
                        'user_id' => auth()->id(),
                        'returned_payment_id' => $response['payment_id'] ?? null,
                        'route_payment_parameter' => $payment,
                        'request_keys' => array_keys($request->all()),
                        'request_data' => $this->sanitizePaymentLogData($request->all()),
                    ]);
                }

                if ($invoice && $invoice->status !== 'paid') {
                    $invoice->status = 'failed';
                    $invoice->save();
                }

                return $this->redirectToPaymentFailed($invoice, $request, $response)
                    ->with('error', __('l.payment_failed'));
            }
        } catch (\Throwable $e) {
            logger('Payment Verification Error: ' . $e->getMessage());

            $invoice = $this->resolveInvoiceFromGatewayReference($request) ?? $invoice;

            if ($invoice?->status === 'paid') {
                return $this->redirectToPaymentSuccess($invoice);
            }

            return $this->redirectToPaymentFailed($invoice, $request)
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

        if ($invoice->status !== 'paid') {
            return $this->redirectToPaymentFailed($invoice, $request);
        }

        return view('themes/default/back.users.payment.success', compact('invoice'));
    }

    /**
     * صفحة فشل الدفع
     */
    public function paymentFailed(Request $request)
    {
        $invoiceId = $this->normalizedGatewayValue($request->get('invoice_id'));
        $paymentReference = $this->gatewayReference($request->all());

        $invoice = null;

        if ($invoiceId !== null && ctype_digit($invoiceId) && (int) $invoiceId > 0) {
            $invoice = Invoice::with(['student', 'lecture', 'course'])
                ->where('user_id', auth()->id())
                ->find($invoiceId);
        }

        $invoice ??= $this->resolveInvoiceFromGatewayReference($request);

        if ($invoice?->status === 'paid') {
            return $this->redirectToPaymentSuccess($invoice);
        }

        if ($invoiceId === null && $paymentReference === null) {
            $recentPaidInvoices = Invoice::with(['student', 'lecture', 'course'])
                ->where('user_id', auth()->id())
                ->where('status', 'paid')
                ->where(function ($query) {
                    $cutoff = now()->subMinutes(10);

                    $query->where('created_at', '>=', $cutoff)
                        ->orWhere('updated_at', '>=', $cutoff);
                })
                ->latest('updated_at')
                ->limit(2)
                ->get();

            if ($recentPaidInvoices->count() === 1) {
                return $this->redirectToPaymentSuccess($recentPaidInvoices->first());
            }
        }

        return view('themes/default/back.users.payment.failed', compact('invoice', 'paymentReference'));
    }

    private function resolveInvoiceFromGatewayReference(Request $request, array $response = []): ?Invoice
    {
        $reference = $this->gatewayReference($response) ?? $this->gatewayReference($request->all());

        if ($reference === null) {
            return null;
        }

        return Invoice::with(['student', 'lecture', 'course'])
            ->where('user_id', auth()->id())
            ->where('pid', $reference)
            ->first();
    }

    private function gatewayReference(array $data): ?string
    {
        foreach (['payment_id', 'merchantOrderId', 'merchant_order_id', 'orderId', 'order_id', 'paymentId'] as $key) {
            $value = $this->normalizedGatewayValue($data[$key] ?? null);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function normalizedGatewayValue($value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', 'undefined'], true)) {
            return null;
        }

        return $value;
    }

    private function redirectToPaymentSuccess(Invoice $invoice)
    {
        return redirect()->route('dashboard.users.payment-success', ['invoice_id' => $invoice->id])
            ->with('success', __('l.payment_successful'));
    }

    private function redirectToPaymentFailed(?Invoice $invoice, Request $request, array $response = [])
    {
        $parameters = [];

        if ($invoice) {
            $parameters['invoice_id'] = $invoice->id;
        } else {
            $reference = $this->gatewayReference($response) ?? $this->gatewayReference($request->all());

            if ($reference !== null) {
                $parameters['payment_id'] = $reference;
            }
        }

        return redirect()->route('dashboard.users.payment-failed', $parameters);
    }

    /**
     * صفحة إلغاء الدفع
     */
    public function paymentCancelled(Request $request)
    {
        return view('themes/default/back.users.payment.cancelled');
    }

    private function sanitizePaymentLogData(array $data): array
    {
        $sensitivePatterns = [
            'card',
            'cvv',
            'cvc',
            'pan',
            'token',
            'secret',
            'key',
            'password',
            'authorization',
            'signature',
        ];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower((string) $key);

            foreach ($sensitivePatterns as $pattern) {
                if (str_contains($normalizedKey, $pattern)) {
                    $data[$key] = '[filtered]';
                    continue 2;
                }
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizePaymentLogData($value);
            }
        }

        return $data;
    }

    private function logKashierPaymentCreation(
        string $stage,
        Request $request,
        $user,
        $amount,
        $gatewayPercentage,
        $gatewayFee,
        $total,
        string $sourceMethods,
        ?array $response = null
    ): void {
        $webhookUrl = (string) config('nafezly-payments.KASHIER_WEBHOOK_URL');

        logger('Kashier payment creation ' . $stage, [
            'flow_type' => $this->getPaymentFlowTypeForLog($request),
            'base_amount' => $amount,
            'gateway_percentage' => $gatewayPercentage,
            'gateway_fee' => $gatewayFee,
            'final_total_sent_to_kashier' => round($total, 2),
            'payment_type' => $request->input('payment_type'),
            'lecture_id' => $request->input('lecture_id'),
            'test_id' => $request->input('test_id'),
            'course_id' => $request->input('course_id'),
            'live_id' => $request->input('live_id'),
            'user_id' => $user?->id,
            'returned_payment_id' => $response['payment_id'] ?? null,
            'html_exists' => isset($response['html']) && $response['html'] !== '',
            'source_methods' => $sourceMethods,
            'kashier_mode' => config('nafezly-payments.KASHIER_MODE'),
            'kashier_currency' => config('nafezly-payments.KASHIER_CURRENCY'),
            'webhook_url_host' => parse_url($webhookUrl, PHP_URL_HOST),
            'redirect_route_name' => config('nafezly-payments.VERIFY_ROUTE_NAME'),
            'redirect_url' => route(config('nafezly-payments.VERIFY_ROUTE_NAME'), ['payment' => 'kashier']),
        ]);
    }

    private function getPaymentFlowTypeForLog(Request $request): string
    {
        if ($request->has('live_id')) {
            return 'live';
        }

        if ($request->has('test_id') || in_array($request->input('payment_type'), ['single_test', 'course_tests'], true)) {
            return 'test';
        }

        if ($request->has('lecture_id')) {
            return 'lecture';
        }

        return 'unknown';
    }

    private function configureKashierGatewayFromDatabase(): bool
    {
        $gateway = PaymentGateway::with('settings')
            ->where('name', 'kashier')
            ->first();

        $settings = $gateway
            ? $gateway->settings->pluck('value', 'key')
            : collect();

        $accountKey = trim((string) $settings->get('KASHIER_ACCOUNT_KEY'));
        $iframeKey = trim((string) $settings->get('KASHIER_IFRAME_KEY'));
        $token = trim((string) $settings->get('KASHIER_TOKEN'));
        $isActive = (bool) ($gateway?->status);

        logger('Kashier gateway configuration check', [
            'gateway_exists' => (bool) $gateway,
            'gateway_active' => $isActive,
            'account_key_present' => $accountKey !== '',
            'iframe_key_present' => $iframeKey !== '',
            'token_present' => $token !== '',
            'mode' => config('nafezly-payments.KASHIER_MODE'),
        ]);

        if (!$gateway || !$isActive || $accountKey === '' || $iframeKey === '' || $token === '') {
            return false;
        }

        config([
            'nafezly-payments.KASHIER_ACCOUNT_KEY' => $accountKey,
            'nafezly-payments.KASHIER_IFRAME_KEY' => $iframeKey,
            'nafezly-payments.KASHIER_TOKEN' => $token,
        ]);

        return true;
    }

    private function kashierGatewayUnavailableResponse()
    {
        logger('Kashier payment blocked because gateway is inactive or credentials are missing.');

        return redirect()->back()
            ->with('error', 'Payment gateway is not available. Please contact support.');
    }

}
