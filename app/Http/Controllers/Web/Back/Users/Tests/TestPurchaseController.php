<?php

namespace App\Http\Controllers\Web\Back\Users\Tests;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Course;
use Illuminate\Http\Request;

class TestPurchaseController extends Controller
{
    /**
     * عرض صفحة شراء اختبار مفرد
     */
    public function showTest($id)
    {
        $user = auth()->user();
        $test = Test::with('course')->findOrFail($id);

        // التحقق من عدم شراء الاختبار مسبقاً
        if ($user->canAccessTest($test->id)) {
            return redirect()->route('dashboard.users.tests.show', $test->id)
                ->with('info', 'لقد اشتريت هذا الاختبار مسبقاً');
        }

        // إعدادات الدفع
        $paymentInfo = [
            'type' => 'single_test',
            'test_id' => $test->id,
            'course_id' => $test->course_id,
            'amount' => $test->price,
            'title' => 'شراء اختبار: ' . $test->name,
            'description' => 'شراء اختبار منفرد من كورس: ' . $test->course->name,
            'invoice_type' => 'single', // للاستخدام في إنشاء الفاتورة
            'category' => 'quiz',
            'type_value' => $test->id
        ];

        return view('themes.default.back.users.tests.purchase-test', compact('test', 'paymentInfo'));
    }

    /**
     * عرض صفحة شراء جميع اختبارات الكورس
     */
    public function showCourseTests($id)
    {
        $user = auth()->user();
        $course = Course::with(['activeTests'])->findOrFail($id);

        // التحقق من وجود اختبارات في الكورس
        if ($course->activeTests->isEmpty()) {
            return redirect()->route('dashboard.users.tests')
                ->with('error', 'لا توجد اختبارات متاحة في هذا الكورس');
        }

        // التحقق من عدم شراء اختبارات الكورس مسبقاً
        if ($user->hasPurchasedCourseQuizzes($course->id)) {
            return redirect()->route('dashboard.users.tests')
                ->with('info', 'لقد اشتريت جميع اختبارات هذا الكورس مسبقاً');
        }

        // حساب السعر الإجمالي للاختبارات المنفردة
        $individualTestsPrice = $course->activeTests->sum('price');

        // سعر شراء الكورس كاملاً (إذا كان محدد)
        $courseTestsPrice = $course->tests_price;

        // حساب المدخرات
        $savings = 0;
        if ($courseTestsPrice && $courseTestsPrice < $individualTestsPrice) {
            $savings = $individualTestsPrice - $courseTestsPrice;
        }

        // إعدادات الدفع
        $paymentInfo = [
            'type' => 'course_tests',
            'course_id' => $course->id,
            'amount' => $courseTestsPrice ?: $individualTestsPrice,
            'title' => 'شراء جميع اختبارات كورس: ' . $course->name,
            'description' => 'شراء جميع الاختبارات المتاحة في الكورس',
            'tests_count' => $course->activeTests->count(),
            'individual_price' => $individualTestsPrice,
            'course_price' => $courseTestsPrice,
            'savings' => $savings,
            'invoice_type' => 'course', // للاستخدام في إنشاء الفاتورة
            'category' => 'quiz'
        ];

        // قائمة الاختبارات المشمولة
        $tests = $course->activeTests->map(function($test) use ($user) {
            return [
                'id' => $test->id,
                'name' => $test->name,
                'description' => $test->description,
                'price' => $test->price,
                'total_score' => $test->total_score,
                'total_questions' => $test->total_questions_count,
                'total_time' => $test->total_time_minutes,
                'has_purchased' => $user->canAccessTest($test->id)
            ];
        });

        return view('themes.default.back.users.tests.purchase-course-tests', compact(
            'course', 'tests', 'paymentInfo'
        ));
    }

    /**
     * مقارنة أسعار الاختبارات المنفردة مع سعر الكورس
     */
    public function comparePrices($courseId)
    {
        $course = Course::with(['activeTests'])->findOrFail($courseId);

        $individualPrice = $course->activeTests->sum('price');
        $coursePrice = $course->tests_price;

        $comparison = [
            'individual_total' => $individualPrice,
            'course_price' => $coursePrice,
            'savings' => $coursePrice ? max(0, $individualPrice - $coursePrice) : 0,
            'is_course_cheaper' => $coursePrice && $coursePrice < $individualPrice,
            'discount_percentage' => $coursePrice && $individualPrice > 0
                ? round((($individualPrice - $coursePrice) / $individualPrice) * 100, 2)
                : 0
        ];

        return response()->json($comparison);
    }

    /**
     * الحصول على معلومات الاختبارات التي لم يشتريها الطالب في كورس معين
     */
    public function getUnpurchasedTests($courseId)
    {
        $user = auth()->user();
        $course = Course::with(['activeTests'])->findOrFail($courseId);

        $unpurchasedTests = $course->activeTests->filter(function($test) use ($user) {
            return !$user->canAccessTest($test->id);
        });

        $testsData = $unpurchasedTests->map(function($test) {
            return [
                'id' => $test->id,
                'name' => $test->name,
                'price' => $test->price,
                'total_score' => $test->total_score,
                'description' => $test->description
            ];
        });

        return response()->json([
            'unpurchased_tests' => $testsData,
            'total_tests' => $course->activeTests->count(),
            'unpurchased_count' => $unpurchasedTests->count(),
            'total_unpurchased_price' => $unpurchasedTests->sum('price')
        ]);
    }

    /**
     * التحقق من حالة شراء الاختبارات في الكورس
     */
    public function checkPurchaseStatus($courseId)
    {
        $user = auth()->user();
        $course = Course::with(['activeTests'])->findOrFail($courseId);

        $purchaseStatus = [
            'has_purchased_course_tests' => $user->hasPurchasedCourseQuizzes($courseId),
            'purchased_individual_tests' => [],
            'unpurchased_tests' => [],
            'total_tests' => $course->activeTests->count(),
            'purchased_count' => 0
        ];

        foreach ($course->activeTests as $test) {
            if ($user->canAccessTest($test->id)) {
                $purchaseStatus['purchased_individual_tests'][] = [
                    'id' => $test->id,
                    'name' => $test->name,
                    'price' => $test->price
                ];
                $purchaseStatus['purchased_count']++;
            } else {
                $purchaseStatus['unpurchased_tests'][] = [
                    'id' => $test->id,
                    'name' => $test->name,
                    'price' => $test->price
                ];
            }
        }

        return response()->json($purchaseStatus);
    }
}
