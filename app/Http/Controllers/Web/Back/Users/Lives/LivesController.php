<?php

namespace App\Http\Controllers\Web\Back\Users\Lives;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Live;
use Yajra\DataTables\Facades\DataTables;

class LivesController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // الحصول على اللايفات المتاحة للمستوى الحالي
        $lives = Live::whereHas('course', function($query) use ($user) {
                $query->where('level_id', $user->level_id);
            })
            ->with(['course'])
            ->get();

        // ✅ ترتيب ذكي للايفات حسب الأولوية
        $lives = $lives->sortBy(function($live) {
            $now = now();

            // 1. اللايفات النشطة (في وقتها) - الأولوية الأولى
            if ($live->start_at && $live->duration) {
                $endTime = $live->start_at->addMinutes($live->duration);
                if ($now >= $live->start_at && $now <= $endTime) {
                    return 1; // الأولوية الأولى
                }
            } elseif ($live->start_at && !$live->duration) {
                if ($now >= $live->start_at) {
                    return 1; // الأولوية الأولى
                }
            } elseif (!$live->start_at) {
                return 1; // الأولوية الأولى (بدون وقت)
            }

            // 2. اللايفات التي لم تبدأ بعد - الأولوية الثانية
            if ($live->start_at && $now < $live->start_at) {
                return 2 + $live->start_at->timestamp; // ترتيب حسب وقت البداية
            }

            // 3. اللايفات التي انتهت - الأولوية الثالثة
            if ($live->start_at && $live->duration) {
                $endTime = $live->start_at->addMinutes($live->duration);
                if ($now > $endTime) {
                    return 3 + $endTime->timestamp; // ترتيب حسب وقت النهاية
                }
            }

            // 4. اللايفات بدون وقت - الأولوية الرابعة
            return 4 + $live->created_at->timestamp; // ترتيب حسب تاريخ الإضافة
        });

        if ($request->ajax()) {
            return DataTables::of($lives)
                ->addIndexColumn()
                ->addColumn('course_name', function($row) {
                    return $row->course ? $row->course->name : '-';
                })
                ->addColumn('type', function($row) {
                    $types = [
                        'free' => '<span class="badge bg-success">' . __('l.Free') . '</span>',
                        'price' => '<span class="badge bg-primary">' . __('l.Paid') . '</span>',
                        'month' => '<span class="badge bg-warning">' . __('l.Monthly') . '</span>',
                        'course' => '<span class="badge bg-info">' . __('l.Course') . '</span>'
                    ];
                    return $types[$row->type] ?? $row->type;
                })
                ->addColumn('price', function($row) {
                    if ($row->type === 'free') {
                        return __('l.Free');
                    }
                    if ($row->type === 'course') {
                        return '-';
                    }
                    if ($row->type === 'month') {
                        return $row->price ? $row->price . ' ' . __('l.Month') : '-';
                    }
                    return $row->price ? $row->price : '-';
                })
                ->addColumn('time_status', function($row) {
                    $now = now();
                    $locale = app()->getLocale();

                    // ✅ التحقق من وقت اللايف
                    if ($row->start_at && $now < $row->start_at) {
                        $startDate = $row->start_at->format('M d, H:i');
                        $daysUntilStart = $now->diffInDays($row->start_at, false);

                        if ($daysUntilStart > 0) {
                            $timeText = $locale === 'ar' ? round($daysUntilStart, 2) . ' يوم' : round($daysUntilStart, 2) . ' days';
                        } else {
                            $timeText = $locale === 'ar' ? round($now->diffInHours($row->start_at, false), 2) . ' ساعة' : round($now->diffInHours($row->start_at, false), 2) . ' hours';
                        }

                        $startText = $locale === 'ar' ? 'يبدأ في' : 'Starts in';

                        return '<span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>
                                    ' . $startText . '<br>
                                    <small>' . $startDate . '</small><br>
                                </span>';
                    }

                    if ($row->start_at && $row->duration) {
                        $endTime = $row->start_at->addMinutes($row->duration);
                        if ($now > $endTime) {
                            $endDate = $endTime->format('M d, H:i');
                            $daysSinceEnd = $now->diffInDays($endTime, false);

                            if ($daysSinceEnd > 0) {
                                $timeText = $locale === 'ar' ? 'منذ ' . round($daysSinceEnd, 2) . ' يوم' : round($daysSinceEnd, 2) . ' days ago';
                            } else {
                                $timeText = $locale === 'ar' ? 'منذ ' . round($now->diffInHours($endTime, false), 2) . ' ساعة' : round($now->diffInHours($endTime, false), 2) . ' hours ago';
                            }

                            $endText = $locale === 'ar' ? 'انتهى في' : 'Ended at';

                            return '<span class="badge bg-danger">
                                        <i class="fas fa-stop-circle me-1"></i>
                                        ' . $endText . '<br>
                                        <small>' . $endDate . '</small><br>
                                    </span>';
                        }
                    }

                    if ($row->start_at) {
                        $timeInfo = [];
                        $timeInfo[] = '<i class="fas fa-play text-success"></i> ' . $row->start_at->format('M d, H:i');

                        if ($row->duration) {
                            $endTime = $row->start_at->addMinutes($row->duration);
                            $timeInfo[] = '<i class="fas fa-stop text-danger"></i> ' . $endTime->format('M d, H:i');
                        }

                        $activeText = $locale === 'ar' ? 'متاح الآن' : 'Available Now';

                        return '<span class="badge bg-success">
                                    <i class="fas fa-play-circle me-1"></i>
                                    ' . $activeText . '<br>
                                    <small>' . implode(' | ', $timeInfo) . '</small>
                                </span>';
                    }

                    $alwaysText = $locale === 'ar' ? 'متاح دائماً' : 'Always Available';
                    $noRestrictionsText = $locale === 'ar' ? 'بدون قيود زمنية' : 'No Time Restrictions';

                    return '<span class="badge bg-info">
                                <i class="fas fa-infinity me-1"></i>
                                ' . $alwaysText . '<br>
                                <small>' . $noRestrictionsText . '</small>
                            </span>';
                })
                ->addColumn('status', function($row) use ($user) {
                    if ($row->type === 'free') {
                        return '<span class="badge bg-success">' . __('l.Available') . '</span>';
                    }

                    if ($row->type === 'price' && $user->hasPaidLive($row->id, $row->course_id)) {
                        return '<span class="badge bg-success">' . __('l.Purchased') . '</span>';
                    }

                    return '<span class="badge bg-warning">' . __('l.Purchase Required') . '</span>';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) use ($user) {
                    if ($user->canAccessLive($row->id, $row->course_id)) {

                        return '<a class="btn btn-primary btn-sm" href="' . route('dashboard.users.lives-show', ['id' => encrypt($row->id)]) . '">
                                    <i class="fas fa-eye me-1"></i>' . __('l.Show Live') . '
                                </a>';
                    } else {
                        return '<a class="btn btn-warning btn-sm" href="' . route('dashboard.users.lives-purchase', ['id' => encrypt($row->id)]) . '">
                                    <i class="fas fa-shopping-cart me-1"></i>' . __('l.Purchase') . '
                                </a>';
                    }
                })
                ->rawColumns(['type', 'price', 'time_status', 'status', 'action'])
                ->make(true);
        }

        return view('themes/default/back/users/lives/lives-list', compact('lives'));
    }

    public function show(Request $request)
    {
        $user = auth()->user();
        $live = Live::with(['course'])
            ->findOrFail(decrypt($request->id));

        // ✅ التحقق من إمكانية الوصول
        if (!$this->canAccessLive($user, $live)) {
            // ✅ التحقق من سبب عدم الصلاحية
            if (!$this->isLiveTimeValid($live)) {
                return redirect()->back()->with('error', $this->getLiveTimeErrorMessage($live));
            }
            return redirect()->route('dashboard.users.lives-purchase', ['id' => encrypt($live->id)]);
        }

        // ✅ التحقق من وقت اللايف
        if (!$this->isLiveTimeValid($live)) {
            return redirect()->back()->with('error', $this->getLiveTimeErrorMessage($live));
        }

        return view('themes/default/back/users/lives/live-show', compact('live'));
    }

    public function purchase(Request $request)
    {
        $live = Live::with('course')->findOrFail(decrypt($request->id));

        // ✅ التحقق من وقت اللايف
        // if (!$this->isLiveTimeValid($live)) {
        //     return redirect()->back()->with('error', $this->getLiveTimeErrorMessage($live));
        // }

        return view('themes/default/back/users/lives/live-purchase', compact('live'));
    }

    /**
     * التحقق من إمكانية الوصول للايف معين
     */
    private function canAccessLive($user, $live)
    {
        // ✅ التحقق من وقت اللايف أولاً
        if (!$this->isLiveTimeValid($live)) {
            return false;
        }

        switch ($live->type) {
            case 'free':
                return true;
            case 'price':
                return $user->hasPaidLive($live->id, $live->course_id);
            case 'month':
                return $user->hasPaidCurrentMonth($live->course_id);
            case 'course':
                return $user->hasPurchasedCourse($live->course_id);
            default:
                return false;
        }
    }

    /**
     * التحقق من صحة وقت اللايف
     */
    private function isLiveTimeValid($live)
    {
        $now = now();

        // ✅ التحقق من وقت البداية
        if ($live->start_at && $now < $live->start_at) {
            return false;
        }

        // ✅ التحقق من وقت النهاية
        if ($live->start_at && $live->duration) {
            $endTime = $live->start_at->addMinutes($live->duration);
            if ($now > $endTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * الحصول على رسالة خطأ وقت اللايف
     */
    private function getLiveTimeErrorMessage($live)
    {
        $now = now();

        // ✅ رسالة وقت البداية
        if ($live->start_at && $now < $live->start_at) {
            $startDate = $live->start_at->format('Y-m-d H:i');
            return __('l.live_not_started_yet', ['start_date' => $startDate]);
        }

        // ✅ رسالة وقت النهاية
        if ($live->start_at && $live->duration) {
            $endTime = $live->start_at->addMinutes($live->duration);
            if ($now > $endTime) {
                $endDate = $endTime->format('Y-m-d H:i');
                return __('l.live_has_ended', ['end_date' => $endDate]);
            }
        }

        return __('l.live_time_not_available');
    }
}