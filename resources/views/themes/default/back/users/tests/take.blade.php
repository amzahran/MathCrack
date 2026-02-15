{{-- resources/views/themes/default/back/users/tests/take.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php
    use Carbon\Carbon;

    $now = Carbon::now();

    // current part number (1, 2, 3, ...)
    $currentPartNumber = 1;
    if (isset($module) && !empty($module->number)) {
        $currentPartNumber = (int) $module->number;
    } elseif (!empty($currentPart) && preg_match('/part(\d+)/', $currentPart, $m)) {
        $currentPartNumber = (int) $m[1];
    }

    // field name for this part duration on tests table
    $currentPartField = "part{$currentPartNumber}_time_minutes";

    // base duration in seconds for this part
    $durationSec = 0;

    // 1) prefer dedicated per part field if present
    if (!empty($test->$currentPartField)) {
        $durationSec = (int) $test->$currentPartField * 60;

    // 2) fallback to known global duration fields
    } elseif (!empty($test->total_time)) {
        $durationSec = (int) $test->total_time * 60;
    } elseif (!empty($test->time)) {
        $durationSec = (int) $test->time * 60;
    } elseif (!empty($test->test_duration)) {
        $durationSec = (int) $test->test_duration * 60;
    } elseif (!empty($test->duration)) {
        $durationSec = (int) $test->duration * 60;
    } elseif (!empty($test->duration_seconds)) {
        $durationSec = (int) $test->duration_seconds;
    } elseif (!empty($test->duration_minutes)) {
        $durationSec = (int) $test->duration_minutes * 60;
    } elseif (!empty($test->time_limit_minutes)) {
        $durationSec = (int) $test->time_limit_minutes * 60;
    } else {
        // 3) last try: sum all part fields if they exist
        $partsMinutes = 0;
        foreach ([
            'part1_time_minutes',
            'part2_time_minutes',
            'part3_time_minutes',
            'part4_time_minutes',
            'part5_time_minutes',
            'break_time_minutes',
        ] as $field) {
            if (!empty($test->$field)) {
                $partsMinutes += (int) $test->$field;
            }
        }

        if ($partsMinutes > 0) {
            $durationSec = $partsMinutes * 60;
        } else {
            // default fallback
            $durationSec = 35 * 60;
        }
    }

    // initial timer value in seconds
    $timerSeconds = $durationSec;

    // respect studentTest remaining_seconds if present
    if (!empty($studentTest)) {
        if (!empty($studentTest->remaining_seconds) && $studentTest->remaining_seconds > 0) {
            $timerSeconds = max(0, (int) $studentTest->remaining_seconds);

        } elseif (!empty($studentTest->end_at)) {
            $endAt = Carbon::parse($studentTest->end_at);
            $timerSeconds = max(0, $now->diffInSeconds($endAt, false));

        } elseif (!empty($studentTest->started_at)) {
            $startedAt = Carbon::parse($studentTest->started_at);
            $endAt = $startedAt->copy()->addSeconds($durationSec);
            $timerSeconds = max(0, $now->diffInSeconds($endAt, false));
        }
    }

    // clamp timerSeconds
    $timerSeconds = max(0, min($timerSeconds, $durationSec));
    $timerSeconds = floor($timerSeconds);

    \Log::info('Timer Calculation per PART - TAKE BLADE', [
        'test_id'                => $test->id ?? 'N/A',
        'test_name'              => $test->name ?? 'N/A',
        'current_part'           => $currentPart ?? null,
        'current_part_number'    => $currentPartNumber,
        'current_part_field'     => $currentPartField,
        'current_part_minutes'   => $test->$currentPartField ?? null,
        'total_time'             => $test->total_time ?? null,
        'calculated_duration_sec'=> $durationSec,
        'final_timer_seconds'    => $timerSeconds,
    ]);

    $testNumber     = $test->number ?? 1;
    $moduleNumber = (isset($module) && !empty($module->number))
    ? (int) $module->number
    : $currentPartNumber;

    // detect how many modules exist (1–5) based on part durations
    $maxModule = 0;
    foreach (range(1, 5) as $i) {
        $field = "part{$i}_time_minutes";
        if (!empty($test->$field)) {
            $maxModule = $i;
        }
    }
    if ($maxModule === 0) {
        $maxModule = max($moduleNumber, 1);
    }

    $totalQuestions = $questions->count() ?? 0;
    $headerTitle    = $test->name ?? $test->title ?? "Digital SAT Practice #{$testNumber} - Math - Module {$moduleNumber}";

    $TIMER_URL_BASE   = route('dashboard.users.tests.remaining-time', ['id' => $test->id]);
    $SUBMIT_PART1_URL = route('dashboard.users.tests.submit-part1', ['id' => $test->id]);
    $SUBMIT_PART2_URL = route('dashboard.users.tests.submit-part2', ['id' => $test->id]);
    $SUBMIT_TEST_URL  = route('dashboard.users.tests.submit', ['id' => $test->id]);
    $SAVE_ANSWER_URL  = route('dashboard.users.tests.save-answer');
    $UPDATE_TIMER_URL = route('dashboard.users.tests.update-timer', ['id' => $test->id]);

    $CURRENT_SUBMIT_URL = $currentPart === 'part1' ? $SUBMIT_PART1_URL : $SUBMIT_PART2_URL;
  @endphp

  <title>{{ $headerTitle }}</title>

  <style>
    :root{
      --bg:#f6f7fb; --ink:#0e1325; --muted:#6b7280; --brand:#2b4bf2; --accent:#1f2937;
      --ok:#059669; --bad:#dc2626; --card:#ffffff; --line:#e5e7eb; --blue700:#1d4ed8;
      --pill:#111827; --warning:#f59e0b; --critical:#dc2626;
    }

    *{box-sizing:border-box; margin:0; padding:0;}
    body{
      margin:0;
      background:var(--bg);
      color:var(--ink);
      font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto;
      line-height:1.6;
    }
    .app{min-height:100vh;display:flex;flex-direction:column}

    .topbar{
      background:#eaf0ff;
      border-bottom:1px solid #dbe3ff;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .topbar-inner{
      max-width:1280px;
      margin:auto;
      display:flex;
      align-items:center;
      gap:16px;
      padding:12px 16px;
      position:relative
    }
    .brand{
      font-weight:700;
      color:#1e3a8a;
      font-size:1.1rem;
    }

    .timer{
      position:absolute;
      left:50%;
      transform:translateX(-50%);
      background:#111827;
      color:#fff;
      border:2px solid #2563eb;
      border-radius:999px;
      padding:8px 20px;
      font-size:18px;
      font-weight:800;
      min-width:100px;
      text-align:center;
      transition: all 0.3s ease;
    }
    .timer.timer-warning{
      background:var(--warning);
      border-color:var(--warning);
      animation: pulse 1.5s infinite;
    }
    .timer.timer-critical{
      background:var(--critical);
      border-color:var(--critical);
      animation:pulse 1s infinite;
    }
    .timer.timer-paused{
      background:#6b7280;
      border-color:#9ca3af;
    }

    .timer-controls {
      display: flex;
      gap: 8px;
      margin-left: auto;
      align-items: center;
    }

    .timer-btn {
      background: #374151;
      color: white;
      border: none;
      border-radius: 999px;
      padding: 10px 22px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 700;
      transition: all 0.2s ease;
      min-width: 90px;
      height: 42px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .timer-btn:hover {
      background: #4b5563;
      transform: translateY(-1px);
    }

    .timer-btn.pause-btn {
      background: #f59e0b;
    }

    .timer-btn.resume-btn {
      background: #059669;
    }

    @keyframes pulse{
      0%{opacity:1}
      50%{opacity:0.7}
      100%{opacity:1}
    }

    .container{
      max-width:1280px;
      margin:16px auto 24px;
      padding:0 20px;
    }

    .banner{
      background:#0f172a;
      color:#fff;
      border-radius:12px;
      padding:14px 20px;
      text-align:center;
      font-weight:700;
      margin-bottom:20px;
    }
    .module-indicator {
      display: inline-flex;
      align-items: center;
      padding: 10px 22px;
      border-radius: 999px;
      background: #020617;
      color: #e5e7eb;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.45);
      border: 1px solid #1e293b;
      gap: 12px;
    }

    .module-indicator-dot {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: #22c55e;
      box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25);
      flex-shrink: 0;
    }

    .module-indicator-text {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .module-indicator-label {
      text-transform: uppercase;
      letter-spacing: 0.15em;
      font-size: 11px;
      color: #9ca3af;
      font-weight: 600;
    }

    .module-indicator-value {
      font-size: 16px;
      font-weight: 700;
      color: #f9fafb;
    }

    .module-indicator-number {
      font-weight: 800;
      color: #facc15;
      margin-left: 4px;
    }

    .module-indicator-total {
      font-size: 13px;
      font-weight: 500;
      color: #9ca3af;
      margin-left: 6px;
    }

    .workspace{
      margin-top:16px;
      display:grid;
      gap:20px;
      align-items:start;
      justify-content:center;
      transition: all 0.3s ease;
    }
    .workspace.no-calc{
      grid-template-columns:minmax(620px, 820px)
    }
    .workspace.with-calc{
      grid-template-columns:480px 1fr
    }

    .calc-pane{
      background:#fff;
      border:1px solid var(--line);
      border-radius:12px;
      overflow:hidden;
      display:none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .calc-pane.show{
      display:block;
      animation: slideIn 0.3s ease;
    }
    .calc-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:12px 16px;
      border-bottom:1px solid var(--line);
      background:#f8fafc;
    }
    .calc-controls{display:flex;gap:8px}
    .calc-body{
      height:560px;
      transition:height 0.3s ease;
      background:#fff;
    }
    .calc-body.expanded{height:680px}
    .calc-iframe{
      width:100%;
      height:100%;
      border:0;
      display:block
    }

    .q-card{
      background:#fff;
      border:1px solid var(--line);
      border-radius:12px;
      overflow:hidden;
      width:100%;
      box-shadow:0 2px 8px rgba(0,0,0,0.08);
    }

    .q-head{
      display:flex;
      align-items:center;
      gap:12px;
      padding:16px 20px;
      border-bottom:1px solid var(--line);
      justify-content:space-between;
      background:#f8fafc;
    }

    .q-head-left, .q-head-right {
      display:flex;
      align-items:center;
      gap:12px;
    }

    .q-num{
      width:36px;
      height:36px;
      display:grid;
      place-items:center;
      border-radius:999px;
      background:#111827;
      color:#fff;
      font-weight:700;
      border:none;
      flex-shrink:0;
      transition: all 0.3s ease;
      font-size:14px;
    }

    .q-num.answered {
      background:#059669;
      transform: scale(1.05);
      box-shadow: 0 2px 8px rgba(5,150,105,0.3);
    }

    .abc-toggle-btn, .mark-pill {
      border:1px solid var(--line);
      border-radius:24px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:8px 14px;
      background:#fff;
      cursor:pointer;
      font-weight:600;
      margin:0;
      transition: all 0.2s ease;
      font-size:14px;
    }

    .abc-toggle-btn:hover, .mark-pill:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .abc-toggle-btn.active{
      background:#fde68a;
      border-color:#f59e0b;
      color:#92400e;
    }
    .mark-pill.active{
      background:#fde68a;
      border-color:#f59e0b;
    }

    .q-body{padding:24px}
    .stem{
      margin:0 0 20px 0;
      line-height:1.7;
      font-size:16px;
      color:#1f2937;
    }

    .options{
      display:grid;
      gap:12px;
      margin-top:12px;
    }
    .option-row {
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
    }

    .option-item{
      display:grid;
      grid-template-columns:42px 1fr;
      gap:14px;
      align-items:start;
      border:2px solid var(--line);
      border-radius:12px;
      padding:14px;
      background:#fff;
      cursor:pointer;
      transition:box-shadow .15s,border-color .15s, transform .15s;
      position:relative;
      flex:1;
    }

    .option-item:hover{
      border-color:#c7d2fe;
      box-shadow:0 0 0 3px rgba(37,99,235,.12);
      transform: translateY(-1px);
    }

    .option-item.selected{
      border-color:#1d4ed8;
      box-shadow:0 0 0 3px rgba(29,78,216,.20);
      background:#f8fbff
    }

    .option-label {
      width:34px;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:2px solid #d1d5db;
      border-radius:999px;
      background:#fff;
      font-weight:700;
      position:relative;
      flex-shrink:0;
      transition: all 0.2s ease;
    }

    .option-text{
      line-height:1.6;
      word-wrap:anywhere;
      font-size:15px;
      flex:1;
      color:#374151;
      width:100%;
    }

    .external-elimination-letter {
      width:30px;
      height:30px;
      display:none;
      align-items:center;
      justify-content:center;
      background:#dc2626;
      color:white;
      border-radius:50%;
      font-size:13px;
      font-weight:bold;
      cursor:pointer;
      z-index:10;
      box-shadow:0 2px 6px rgba(0,0,0,0.3);
      border:2px solid #fff;
      transition: all 0.2s ease;
      flex-shrink:0;
    }

    .external-elimination-letter:hover {
      transform: scale(1.15);
      background:#b91c1c;
      box-shadow: 0 4px 8px rgba(0,0,0,0.4);
    }

    .external-elimination-letter.eliminated {
      background: #059669;
      text-decoration: line-through;
    }

    .option-strike {
      position:absolute;
      top:50%;
      left:54px;
      right:16px;
      height:3px;
      background:#dc2626;
      display:none;
      z-index:5;
      transform:translateY(-50%);
    }

    .option-item.eliminated {
      opacity:0.6;
      background:#fef2f2;
      border-color:#fecaca;
      cursor: not-allowed;
    }

    .option-item.eliminated .option-text {color:#9ca3af;}
    .option-item.eliminated .option-label {opacity: 0.6;}
    .option-item.selected .option-label {border-color:#1d4ed8; color:#1d4ed8;}

    .elimination-mode-active .external-elimination-letter {display: flex;}
    .elimination-mode-active .option-item.eliminated .option-strike {display: block;}

    .q-nav{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      padding:16px 20px;
      border-top:1px solid var(--line);
      justify-content:flex-end;
      background:#f8fafc;
    }
    .q-nav-buttons{display:flex;gap:10px}

    .questions-bar {
      background:#111827;
      color:white;
      padding:14px 24px;
      border-top:2px solid #374151;
      position:fixed;
      bottom:0;
      left:0;
      right:0;
      z-index:1000;
      box-shadow:0 -4px 12px rgba(0,0,0,0.3);
    }

    .questions-bar-inner {
      max-width:1280px;
      margin:0 auto;
      display:flex;
      align-items:center;
      gap:14px;
      flex-wrap:wrap;
    }

    .questions-bar-title {
      font-weight:700;
      font-size:14px;
      color:#d1d5db;
      white-space:nowrap;
    }

    .questions-scroll-container {
      flex:1;
      overflow-x:auto;
      padding:4px 0;
    }

    .questions-numbers {
      display:flex;
      gap:6px;
      align-items:center;
    }

    .question-bar-btn {
      min-width:38px;
      height:38px;
      border:2px solid #374151;
      background:#1f2937;
      color:#f3f4f6;
      border-radius:8px;
      cursor:pointer;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:600;
      font-size:14px;
      transition:all 0.2s ease;
      flex-shrink:0;
    }

    .question-bar-btn:hover{
      background:#374151;
      border-color:#4b5563;
      transform: translateY(-1px);
    }
    .question-bar-btn.current{
      background:#2563eb;
      border-color:#3b82f6;
      color:white;
      transform: scale(1.05);
    }
    .question-bar-btn.answered{
      background:#059669;
      border-color:#10b981;
      color:white;
    }
    .question-bar-btn.marked{
      background:#d97706;
      border-color:#f59e0b;
      color:white;
    }

    .content-wrapper{padding-bottom:90px}

    .footer {
      margin-top:auto;
      border-top:1px solid var(--line);
      background:#fff;
      position:relative;
      z-index:999;
    }

    .footer-inner {
      max-width:1280px;
      margin:auto;
      display:flex;
      align-items:center;
      gap:12px;
      padding:14px 20px;
    }

    .pill {
      margin-left:auto;
      background:#111827;
      color:#fff;
      border-radius:999px;
      padding:8px 14px;
      font-weight:700;
      font-size:14px;
    }

    .btn {
      background:#1d4ed8;
      color:#fff;
      border:none;
      border-radius:10px;
      padding:12px 20px;
      cursor:pointer;
      font-size:14px;
      font-weight:600;
      transition: all 0.2s ease;
    }

    .btn:hover {
      background:#2563eb;
      transform: translateY(-1px);
      box-shadow: 0 2px 6px rgba(37,99,235,0.3);
    }

    .btn-sm {
      background:#374151;
      color:#fff;
      border:none;
      border-radius:6px;
      padding:8px 14px;
      cursor:pointer;
      font-size:12px;
      transition: all 0.2s ease;
    }

    .btn-sm:hover {
      background:#4b5563;
      transform: translateY(-1px);
    }
/* ============================================= */
.question-image {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}

.question-image img {
    max-width: 80%;
    max-height: 400px;
    width: auto;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    object-fit: contain;
}
/* =================================================== */
    /* Question image styling - BEFORE question text
    .question-image {
      margin: 0 0 20px 0;
      text-align: center;
      width: 50%;
    }

    .question-image img {
      max-width: 80% !important;
      width: auto !important;
      height: auto !important;
      max-height: 400px;
      display: block;
      margin: 0 auto 20px auto;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      object-fit: contain;
    } */

    /* Option images styling */
    .option-image {
      margin: 10px 0 0 0;
      text-align: center;
      width: 100%;
    }

    .option-image img {
      max-width: 80% !important;
      width: auto !important;
      height: auto !important;
      max-height: 200px;
      display: block;
      margin: 10px auto 0 auto;
      border-radius: 6px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
      object-fit: contain;
    }

    /* Make sure option text images appear */
    .option-text img {
      max-width: 80% !important;
      width: auto !important;
      height: auto !important;
      max-height: 200px;
      display: block;
      margin: 10px auto 0 auto;
      border-radius: 6px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
      object-fit: contain;
    }

    /* Images in stem/question text */
    .stem img {
      max-width: 80% !important;
      width: auto !important;
      height: auto !important;
      max-height: 300px;
      display: block;
      margin: 15px auto;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      object-fit: contain;
    }

    .stem .MathJax_Display,
    .stem .mjx-chtml[display="true"] {
      margin:0.1rem 0 0.3rem 0 !important;
      padding:0 !important;
      line-height:1 !important;
    }

    .stem{line-height:1.7 !important}
    .stem p{margin:0.3rem 0 !important}

    .ref-modal {
      width:min(1200px, 95vw);
      height:min(800px, 90vh);
      background:#fff;
      border-radius:16px;
      border:1px solid #d1d5db;
      overflow:hidden;
      display:flex;
      flex-direction:column;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .ref-modal-header {
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:16px 20px;
      border-bottom:1px solid #e5e7eb;
      background:#0f172a;
      color:#fff;
      flex-shrink:0;
    }

    .ref-modal-body {
      flex:1;
      padding:0;
      overflow:hidden;
    }

    .pdf-iframe {
      width:100%;
      height:100%;
      border:none;
      display:block;
    }

    .warning-modal-backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
      z-index: 10000;
      align-items: center;
      justify-content: center;
      animation: fadeIn 0.3s ease;
    }

    .warning-modal {
      background: white;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      width: 90%;
      max-width: 480px;
      overflow: hidden;
      animation: slideUp 0.3s ease;
      border: 1px solid #e5e7eb;
    }

    .modal-header {
      background: linear-gradient(135deg, #fef3c7, #f59e0b);
      padding: 24px;
      text-align: center;
      position: relative;
    }

    .warning-icon {
      font-size: 52px;
      margin-bottom: 12px;
    }

    .modal-title {
      font-size: 22px;
      font-weight: 700;
      color: #92400e;
      margin: 0;
    }

    .modal-body {
      padding: 28px;
      text-align: center;
    }

    .unanswered-count {
      font-size: 52px;
      font-weight: 800;
      color: #dc2626;
      margin: 12px 0;
    }

    .unanswered-text {
      font-size: 18px;
      color: #374151;
      margin-bottom: 20px;
      line-height: 1.5;
    }

    .questions-preview {
      background: #f8fafc;
      border-radius: 12px;
      padding: 18px;
      margin: 20px 0;
      border: 1px solid #e5e7eb;
    }

    .questions-scroll {
      max-height: 120px;
      overflow-y: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: center;
    }

    .question-bubble {
      background: white;
      border: 2px solid #dc2626;
      border-radius: 20px;
      padding: 6px 12px;
      font-size: 14px;
      font-weight: 600;
      color: #dc2626;
      min-width: 40px;
      text-align: center;
    }

    .modal-footer {
      padding: 20px 24px;
      display: flex;
      gap: 12px;
      justify-content: center;
    }

    .modal-btn {
      padding: 12px 24px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      min-width: 130px;
    }

    .btn-cancel {
      background: #6b7280;
      color: white;
    }

    .btn-cancel:hover {
      background: #4b5563;
      transform: translateY(-1px);
    }

    .btn-submit {
      background: #dc2626;
      color: white;
    }

    .btn-submit:hover {
      background: #b91c1c;
      transform: translateY(-1px);
    }

    .numeric-answer-wrapper{
      margin-top:1.5rem;
      display:flex;
      justify-content:flex-start;
    }

    .numeric-answer-box{
      position: relative;
      width: 50%;
      max-width: 160px;
      padding: 20px 20px 32px;
      border-radius: 10px;
      border: 1.6px solid #111827;
      background: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .numeric-answer-box::after{
      content:"";
      position:absolute;
      left:18px;
      right:18px;
      bottom:14px;
      height:2px;
      background:#111827;
      border-radius:999px;
    }

    .numeric-answer-input{
      border:none;
      outline:none;
      background:transparent;
      width:100%;
      font-size:18px;
      text-align:center;
      padding:0;
      margin:0;
      letter-spacing:1px;
      font-weight:600;
    }

    .numeric-answer-input::-webkit-outer-spin-button,
    .numeric-answer-input::-webkit-inner-spin-button{
      -webkit-appearance:none;
      margin:0;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes pulse2 {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); background: #dc2626; }
      100% { transform: scale(1); }
    }

    .pulse {animation: pulse2 2s infinite;}

    @media (max-width:768px){
      .workspace.no-calc{grid-template-columns:minmax(300px, 1fr)}
      .workspace.with-calc{grid-template-columns:1fr}

      .q-head {
        flex-direction:column;
        gap:10px;
        align-items:stretch;
      }

      .q-head-left, .q-head-right{justify-content:center}

      .option-row {
        gap: 6px;
      }

      .external-elimination-letter {
        width: 26px;
        height: 26px;
        font-size: 11px;
      }

      .modal-footer {
        flex-direction: column;
      }

      .modal-btn {
        min-width: auto;
      }

      .numeric-answer-box {
        width: 70%;
      }

      .timer-controls {
        flex-direction: column;
        gap: 4px;
      }

      .container {
        padding: 0 16px;
      }
      
      /* Mobile images */
      .question-image img,
      .stem img {
        max-width: 95% !important;
        max-height: 250px !important;
      }
      
      .option-text img,
      .option-image img {
        max-width: 95% !important;
        max-height: 150px !important;
      }
    }

    @media (max-width:640px){
      .timer {
        font-size:14px;
        padding:6px 14px;
        position:static;
        transform:none;
        margin-left:auto;
      }

      .topbar-inner {
        flex-wrap:wrap;
        justify-content:space-between;
        gap:10px;
      }

      .warning-modal {
        margin: 20px;
      }

      .numeric-answer-box {
        width: 80%;
      }

      .q-body {
        padding: 18px;
      }

      .footer-inner {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }

      .pill {
        margin-left: 0;
      }
      
      .question-image img,
      .stem img {
        max-height: 200px !important;
      }
      
      .option-text img,
      .option-image img {
        max-height: 120px !important;
      }
    }

    @media (max-width:480px){
      .option-item {
        grid-template-columns: 36px 1fr;
        gap: 10px;
        padding: 12px;
      }

      .q-head {
        padding: 12px 16px;
      }

      .questions-bar {
        padding: 12px 16px;
      }

      .question-bar-btn {
        min-width: 34px;
        height: 34px;
        font-size: 12px;
      }
    }
  </style>

  <script src="https://www.desmos.com/api/v1.10/calculator.js?apiKey=dcb31709b452b1cf9dc26972add0fda6"></script>
</head>

<body>
<div class="app">
  <div class="topbar">
    <div class="topbar-inner">
      <div class="brand">{{ $headerTitle }}</div>

      <div class="timer" id="timer-display">--:--</div>

      <div class="timer-controls">
        <button
          type="button"
          class="timer-btn pause-btn"
          id="pauseTimerBtn"
        >
          Pause
        </button>

        <button
          type="button"
          class="timer-btn resume-btn"
          id="resumeTimerBtn"
          style="display:none"
        >
          Resume
        </button>

        <button
          type="button"
          class="btn-sm"
          id="btnCalc"
        >
          🧮 Calculator
        </button>

        <button
          type="button"
          class="btn-sm"
          id="btnRef"
        >
          📄 Reference
        </button>
      </div>
    </div>
  </div>

  <div class="content-wrapper">
    <div class="container">
      <div class="banner">THIS IS A PRACTICE TEST</div>

      <div class="module-indicator">
        <div class="module-indicator-dot"></div>
        <div class="module-indicator-text">
          <span class="module-indicator-label">Current module</span>
          <span class="module-indicator-value">
            Module
            <span class="module-indicator-number">{{ $moduleNumber }}</span>
            @if($maxModule > 1)
              <span class="module-indicator-total">of {{ $maxModule }}</span>
            @endif
          </span>
        </div>
      </div>

      <div class="workspace no-calc" id="workspace">
        <aside id="calcPane" class="calc-pane" aria-label="Calculator">
          <div class="calc-header">
            <div class="calc-controls">
              <button id="btnToggleKeypad" class="btn-sm">⌨️ Show Keypad</button>
              <button id="btnExpandCalc" class="btn-sm">↕️ Expand</button>
            </div>
            <button id="btnCloseCalc" class="btn" style="background:#111827">Close</button>
          </div>
          <div class="calc-body" id="calcBody">
            <div id="desmos" style="width:100%;height:100%;">
              <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f8fafc;color:#666;font-size:16px;">
                Loading Calculator...
              </div>
            </div>
          </div>
        </aside>

        <div class="q-card" id="qCard">
          <div class="q-head">
            <div class="q-head-left">
              <div class="q-num" id="current-question-display">1</div>
              <button type="button" id="btnMark" class="mark-pill">🔖 Mark for Review</button>
            </div>
            <div class="q-head-right">
              <button type="button" id="btnABC" class="abc-toggle-btn">✏️ Elimination Mode</button>
            </div>
          </div>

          <form id="answerForm">
            <input type="hidden" id="student_test_id" value="{{ $studentTest->id ?? (auth()->id().'-'.$test->id) }}">
            <input type="hidden" name="question_id" id="question_id" value="">
            <input type="hidden" id="markedField" value="0">

            <div class="q-body">
              @foreach($questions as $i => $q)
                <div class="question-item" data-question-index="{{ $i }}" data-question-id="{{ $q->id }}" style="display: {{ $i === 0 ? 'block':'none' }};">
                  {{-- Question image appears FIRST, before the text --}}
                  @if(!empty($q->question_image))
                    <div class="question-image">
                      <img src="{{ asset($q->question_image) }}" 
                           alt="Question Image"
                           onerror="this.style.display='none';">
                    </div>
                  @endif

                  <div class="stem">
                    {!! nl2br($q->question_text) !!}
                  </div>

                  @if($q->type === 'mcq')
                    <div class="options options-container">
                      @if(isset($q->options) && count($q->options) > 0)
                        @foreach($q->options as $opt)
                          @php
                            // الحصول على صورة الخيار من أي حقل متاح
                            $optionImage = $opt->image ?? $opt->option_image ?? null;
                          @endphp
                          <div class="option-row">
                            <div class="option-item" data-option-id="{{ $opt->id }}" onclick="selectMCQOption(this, {{ $q->id }})">
                              <div class="option-label">
                                <span>{{ $opt->label ?? chr(64 + $loop->iteration) }}</span>
                              </div>
                              <div class="option-text">
                                {!! nl2br($opt->option_text) !!}
                                
                                {{-- Option image appears AFTER the option text --}}
                                @if($optionImage)
                                  <div class="option-image">
                                    <img src="{{ asset($optionImage) }}" 
                                         alt="Option Image"
                                         onerror="this.style.display='none';">
                                  </div>
                                @endif
                              </div>
                              <div class="option-strike"></div>
                            </div>
                            <div class="external-elimination-letter" data-letter="{{ $opt->label ?? chr(64 + $loop->iteration) }}">
                              {{ $opt->label ?? chr(64 + $loop->iteration) }}
                            </div>
                          </div>
                        @endforeach
                      @else
                        {{-- Fallback options --}}
                        <div class="option-row">
                          <div class="option-item" data-option-id="1" onclick="selectMCQOption(this, {{ $q->id }})">
                            <div class="option-label">
                              <span>A</span>
                            </div>
                            <div class="option-text">\( P = 63n - 120,000 \)</div>
                            <div class="option-strike"></div>
                          </div>
                          <div class="external-elimination-letter" data-letter="A">A</div>
                        </div>
                        <div class="option-row">
                          <div class="option-item" data-option-id="2" onclick="selectMCQOption(this, {{ $q->id }})">
                            <div class="option-label">
                              <span>B</span>
                            </div>
                            <div class="option-text">\( P = 63n + 120,000 \)</div>
                            <div class="option-strike"></div>
                          </div>
                          <div class="external-elimination-letter" data-letter="B">B</div>
                        </div>
                        <div class="option-row">
                          <div class="option-item" data-option-id="3" onclick="selectMCQOption(this, {{ $q->id }})">
                            <div class="option-label">
                              <span>C</span>
                            </div>
                            <div class="option-text">\( P = 63(n - 120,000) \)</div>
                            <div class="option-strike"></div>
                          </div>
                          <div class="external-elimination-letter" data-letter="C">C</div>
                        </div>
                        <div class="option-row">
                          <div class="option-item" data-option-id="4" onclick="selectMCQOption(this, {{ $q->id }})">
                            <div class="option-label">
                              <span>D</span>
                            </div>
                            <div class="option-text">\( P = 63(n + 120,000) \)</div>
                            <div class="option-strike"></div>
                          </div>
                          <div class="external-elimination-letter" data-letter="D">D</div>
                        </div>
                      @endif
                    </div>
                  @elseif($q->type === 'tf')
                    <div class="options tf-options">
                      <div class="option-row">
                        <div class="option-item tf-option" data-value="True" onclick="selectTFOption(this, {{ $q->id }})">
                          <div class="option-label">
                            <span>T</span>
                          </div>
                          <div class="option-text">True</div>
                          <div class="option-strike"></div>
                        </div>
                        <div class="external-elimination-letter" data-letter="T">T</div>
                      </div>
                      <div class="option-row">
                        <div class="option-item tf-option" data-value="False" onclick="selectTFOption(this, {{ $q->id }})">
                          <div class="option-label">
                            <span>F</span>
                          </div>
                          <div class="option-text">False</div>
                          <div class="option-strike"></div>
                        </div>
                        <div class="external-elimination-letter" data-letter="F">F</div>
                      </div>
                    </div>
                  @elseif($q->type === 'numeric')
                    <div class="numeric-answer-wrapper">
                      <div class="numeric-answer-box">
                        <input
                          class="numeric-answer-input"
                          type="text"
                          inputmode="numeric"
                          pattern="[0-9]*"
                          autocomplete="off"
                          oninput="
                            this.value = this.value.replace(/[^0-9]/g, '');
                            saveNumericAnswer(this, {{ $q->id }});
                          "
                        >
                      </div>
                    </div>
                  @endif
                  
            </div>
              @endforeach
            </div>

            <div class="q-nav">
              <div class="q-nav-buttons">
                <button type="button" class="btn" id="prev-btn" onclick="previousQuestion()">Previous</button>
                <button type="button" class="btn" id="next-btn" onclick="nextQuestion()">Next</button>
                <button type="button" class="btn" id="submit-btn" style="display:none" onclick="submitPart()">
                  Submit Test
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="questions-bar">
    <div class="questions-bar-inner">
      <div class="questions-bar-title">Questions:</div>
      <div class="questions-scroll-container">
        <div class="questions-numbers" id="questionsBarNumbers">
          @foreach($questions as $i => $q)
            <button type="button" class="question-bar-btn {{ $i===0 ? 'current' : '' }}"
                    data-question-index="{{ $i }}"
                    onclick="goToQuestion({{ $i }})">
              {{ $i+1 }}
            </button>
          @endforeach
        </div>
      </div>
      <button class="btn" onclick="nextQuestion()" style="background:#374151;padding:8px 16px;font-size:14px">Next</button>
    </div>
  </div>

  <div class="footer">
    <div class="footer-inner">
      <div>{{ auth()->user()->name ?? 'Student' }}</div>
      <div class="pill">Question <span id="current-question-number">1</span> of {{ $totalQuestions }}</div>
      <button class="btn" onclick="nextQuestion()" style="margin-left:auto">Next</button>
    </div>
  </div>

  <div id="refBackdrop" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.85);z-index:2000;align-items:center;justify-content:center">
    <div class="ref-modal">
      <div class="ref-modal-header">
        <h3 style="margin:0;font-size:22px">Reference Sheet</h3>
        <button id="refClose" style="border:none;background:transparent;color:#fff;font-size:22px;width:36px;height:36px;border-radius:8px;cursor:pointer">×</button>
      </div>
      <div class="ref-modal-body">
        <iframe
          src="{{ asset('Pdfs/References.pdf') }}#toolbar=0&navpanes=0&scrollbar=0"
          class="pdf-iframe"
          title="SAT Reference Sheet">
        </iframe>
      </div>
    </div>
  </div>

  <div id="warningModal" class="warning-modal-backdrop">
    <div class="warning-modal">
      <div class="modal-header">
        <div class="warning-icon">⚠️</div>
        <h2 class="modal-title">Unanswered Questions</h2>
      </div>

      <div class="modal-body">
        <div class="unanswered-count" id="unansweredCount">0</div>
        <div class="unanswered-text">
          You have unanswered questions in your test
        </div>

        <div class="questions-preview">
          <div style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">
            Unanswered questions:
          </div>
          <div class="questions-scroll" id="questionsList"></div>
        </div>

        <div style="color: #6b7280; font-size: 14px;">
          Are you sure you want to submit the test
        </div>
      </div>

      <div class="modal-footer">
        <button class="modal-btn btn-cancel" onclick="closeWarningModal()">
          Cancel
        </button>
        <button class="modal-btn btn-submit pulse" id="submitAnywayBtn">
          Submit Anyway
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  window.MathJax = {
    tex: {
      inlineMath:[['\\(','\\)'],['$','$']],
      displayMath:[['\\[','\\]'],['$$','$$']],
      processEscapes:true,
      processEnvironments:true
    },
    options: {
      skipHtmlTags:['script','noscript','style','textarea','pre','code'],
      ignoreHtmlClass:'tex-ignore',
      processHtmlClass:'tex-process'
    },
    svg: {
      fontCache:'global',
      scale:0.9,
      displayAlign:'center'
    }
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>

<script>
  const TestConfig = {
    TEST_ID: @json($test->id ?? ($test_id ?? request()->route('id'))),
    currentPart: @json($currentPart ?? 'part1'),
    totalQuestions: {{ (int)($questions->count() ?? 0) }},
    studentTestId: '{{ $studentTest->id ?? (auth()->id()."-".$test->id) }}',

    URLs: {
      TIMER_BASE: @json($TIMER_URL_BASE),
      SUBMIT_PART1: @json($SUBMIT_PART1_URL),
      SUBMIT_PART2: @json($SUBMIT_PART2_URL),
      SUBMIT_TEST: @json($SUBMIT_TEST_URL),
      SAVE_ANSWER: @json($SAVE_ANSWER_URL),
      UPDATE_TIMER: @json($UPDATE_TIMER_URL)
    },

    Timer: {
      remaining: Math.floor({{ $timerSeconds }}),
      isPaused: false,
      interval: null,
      lastUpdate: Date.now()
    }
  };

  const TestState = {
    currentQuestionIndex: 0,
    answeredQuestions: new Set(),
    markedQuestions: new Set(),
    eliminationMode: false,
    eliminatedOptions: new Map(),
    isAutoNavigating: false,

    isLastQuestion() {
      return this.currentQuestionIndex === TestConfig.totalQuestions - 1;
    },

    getUnansweredCount() {
      return TestConfig.totalQuestions - this.answeredQuestions.size;
    },

    updateQuestionStatus(index, isAnswered = false, isMarked = false) {
      if (isAnswered) this.answeredQuestions.add(index);
      else this.answeredQuestions.delete(index);

      if (isMarked) this.markedQuestions.add(index);
      else this.markedQuestions.delete(index);
    }
  };

  const TimerSystem = {
    init() {
      this.updateDisplay(TestConfig.Timer.remaining);
      this.start();
    },

    start() {
      clearInterval(TestConfig.Timer.interval);
      this.updateDisplay(TestConfig.Timer.remaining);
      TestConfig.Timer.lastUpdate = Date.now();

      TestConfig.Timer.interval = setInterval(() => {
        if (!TestConfig.Timer.isPaused) {
          const now = Date.now();
          const elapsedSeconds = Math.floor((now - TestConfig.Timer.lastUpdate) / 1000);
          TestConfig.Timer.lastUpdate = now;

          TestConfig.Timer.remaining = Math.max(0, TestConfig.Timer.remaining - elapsedSeconds);
          this.updateDisplay(TestConfig.Timer.remaining);

          if (TestConfig.Timer.remaining % 30 === 0) {
            this.saveState();
          }

          if (TestConfig.Timer.remaining === 0) {
            this.handleTimeUp();
          }
        }
      }, 1000);
    },

    updateDisplay(seconds) {
      const minutes = Math.floor(seconds / 60);
      const secs = seconds % 60;
      const el = document.getElementById('timer-display');

      el.textContent = `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

      el.classList.remove('timer-warning', 'timer-critical');

      if (seconds <= 60) {
        el.classList.add('timer-critical');
      } else if (seconds <= 300) {
        el.classList.add('timer-warning');
      }
    },

    pause() {
      if (TestConfig.Timer.isPaused) return;

      TestConfig.Timer.isPaused = true;
      clearInterval(TestConfig.Timer.interval);

      document.getElementById('timer-display').classList.add('timer-paused');
      document.getElementById('pauseTimerBtn').style.display = 'none';
      document.getElementById('resumeTimerBtn').style.display = 'inline-flex';

      this.saveState();
    },

    resume() {
      if (!TestConfig.Timer.isPaused) return;

      TestConfig.Timer.isPaused = false;
      TestConfig.Timer.lastUpdate = Date.now();

      document.getElementById('timer-display').classList.remove('timer-paused');
      document.getElementById('pauseTimerBtn').style.display = 'inline-flex';
      document.getElementById('resumeTimerBtn').style.display = 'none';

      this.start();
      this.saveState();
    },

    saveState() {
      if (!TestConfig.URLs.UPDATE_TIMER) {
        return;
      }

      fetch(TestConfig.URLs.UPDATE_TIMER, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          student_test_id: TestConfig.studentTestId,
          remaining_seconds: TestConfig.Timer.remaining,
          is_paused: TestConfig.Timer.isPaused
        })
      })
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            console.error('Error saving timer state:', data.error);
          }
        })
        .catch(error => {
          console.error('Error saving timer state:', error);
        });
    },

    handleTimeUp() {
      clearInterval(TestConfig.Timer.interval);
      this.showTimeUpAlert();
    },

    showTimeUpAlert() {
      const alertBox = document.createElement('div');
      alertBox.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #dc2626;
        color: white;
        padding: 20px 30px;
        border-radius: 12px;
        z-index: 10000;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        font-weight: 600;
      `;
      alertBox.innerHTML = `
        <div style="font-size: 18px; margin-bottom: 10px;">⏰ Time is up</div>
        <div>Submitting your test automatically</div>
      `;
      document.body.appendChild(alertBox);

      setTimeout(() => {
        document.body.removeChild(alertBox);
        SubmissionSystem.proceedWithSubmission();
      }, 3000);
    },

    getHeaders() {
      return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      };
    }
  };

  const AnswerSystem = {
    async saveAnswer(questionId, kind, value) {
      const payload = {
        student_test_id: TestConfig.studentTestId,
        question_id: questionId,
        answer: (kind === 'mcq') ? { option_id: value } : { text: String(value ?? '') }
      };

      try {
        const response = await fetch(TestConfig.URLs.SAVE_ANSWER, {
          method: 'POST',
          headers: this.getHeaders(),
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
          TestState.updateQuestionStatus(TestState.currentQuestionIndex, true);
          NavigationSystem.updateUI();
        } else {
          console.error('Error saving answer:', data.error);
        }
      } catch (error) {
        console.error('Error saving answer:', error);
      }
    },

    selectMCQOption(el, qid) {
      if (el.classList.contains('eliminated')) return;

      el.closest('.options-container').querySelectorAll('.option-item').forEach(x => {
        x.classList.remove('selected');
      });

      el.classList.add('selected');
      this.saveAnswer(qid, 'mcq', el.dataset.optionId);
    },

    selectTFOption(el, qid) {
      if (el.classList.contains('eliminated')) return;

      el.closest('.tf-options').querySelectorAll('.tf-option').forEach(x => {
        x.classList.remove('selected');
      });

      el.classList.add('selected');
      this.saveAnswer(qid, 'text', el.dataset.value);
    },

    saveNumericAnswer(el, qid) {
      if (el.value.trim() !== '') {
        this.saveAnswer(qid, 'text', el.value.trim());
      }
    },

    getHeaders() {
      return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      };
    }
  };

  const NavigationSystem = {
    goToQuestion(i) {
      if (i < 0 || i >= TestConfig.totalQuestions) return;

      document.querySelectorAll('.question-item').forEach(el => {
        el.style.display = 'none';
      });

      const target = document.querySelector(`.question-item[data-question-index="${i}"]`);
      if (target) target.style.display = 'block';

      TestState.currentQuestionIndex = i;

      if (window.MathJax?.typesetPromise) {
        MathJax.typesetPromise();
      }

      this.updateUI();

      const currentBtn = document.querySelector(`.question-bar-btn[data-question-index="${i}"]`);
      if (currentBtn) {
        currentBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      }
    },

    nextQuestion() {
      if (TestState.currentQuestionIndex < TestConfig.totalQuestions - 1) {
        this.goToQuestion(TestState.currentQuestionIndex + 1);
      }
    },

    previousQuestion() {
      if (TestState.currentQuestionIndex > 0) {
        this.goToQuestion(TestState.currentQuestionIndex - 1);
      }
    },

    updateUI() {
      const n = TestState.currentQuestionIndex + 1;
      document.getElementById('current-question-display').textContent = n;
      document.getElementById('current-question-number').textContent = n;

      const qNumDisplay = document.getElementById('current-question-display');
      if (TestState.answeredQuestions.has(TestState.currentQuestionIndex)) {
        qNumDisplay.classList.add('answered');
      } else {
        qNumDisplay.classList.remove('answered');
      }

      document.querySelectorAll('.question-bar-btn').forEach(btn => {
        const questionIndex = +btn.dataset.questionIndex;
        btn.classList.toggle('current', questionIndex === TestState.currentQuestionIndex);
        btn.classList.toggle('answered', TestState.answeredQuestions.has(questionIndex));
        btn.classList.toggle('marked', TestState.markedQuestions.has(questionIndex));
      });

      document.getElementById('prev-btn').disabled = TestState.currentQuestionIndex === 0;
      document.getElementById('next-btn').style.display = TestState.isLastQuestion() ? 'none' : 'inline-block';
      document.getElementById('submit-btn').style.display = TestState.isLastQuestion() ? 'inline-block' : 'none';

      EliminationSystem.updateEliminationState();
    }
  };

  const EliminationSystem = {
    toggleMode() {
      TestState.eliminationMode = !TestState.eliminationMode;
      const btnABC = document.getElementById('btnABC');
      const qCard = document.getElementById('qCard');

      if (btnABC) {
        btnABC.classList.toggle('active', TestState.eliminationMode);
        btnABC.innerHTML = TestState.eliminationMode ?
          '✏️ Elimination Mode (ON)' :
          '✏️ Elimination Mode';
      }

      if (qCard) {
        qCard.classList.toggle('elimination-mode-active', TestState.eliminationMode);
      }

      if (TestState.eliminationMode) {
        this.showGuide();
      }
    },

    showGuide() {
      const tooltip = document.createElement('div');
      tooltip.style.cssText = `
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        z-index: 10000;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        text-align: center;
      `;
      tooltip.innerHTML = `
        <strong>Elimination mode ON</strong><br>
        Click the red letters to eliminate options<br>
        <small>You can still select non eliminated options</small>
      `;
      document.body.appendChild(tooltip);

      setTimeout(() => {
        if (tooltip.parentNode) {
          tooltip.parentNode.removeChild(tooltip);
        }
      }, 4000);
    },

    setup() {
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('external-elimination-letter')) {
          if (!TestState.eliminationMode) return;

          const letter = e.target.getAttribute('data-letter');
          const optionRow = e.target.closest('.option-row');
          const optionItem = optionRow.querySelector('.option-item');

          if (optionItem.classList.contains('eliminated')) {
            this.uneliminateOption(optionItem, e.target, letter);
          } else {
            this.eliminateOption(optionItem, e.target, letter);
          }

          e.stopPropagation();
        }
      });
    },

    eliminateOption(optionItem, letterElement, letter) {
      optionItem.classList.add('eliminated');
      letterElement.classList.add('eliminated');
      letterElement.style.background = '#059669';

      if (!TestState.eliminatedOptions.has(TestState.currentQuestionIndex)) {
        TestState.eliminatedOptions.set(TestState.currentQuestionIndex, new Set());
      }
      TestState.eliminatedOptions.get(TestState.currentQuestionIndex).add(letter);
    },

    uneliminateOption(optionItem, letterElement, letter) {
      optionItem.classList.remove('eliminated');
      letterElement.classList.remove('eliminated');
      letterElement.style.background = '#dc2626';

      if (TestState.eliminatedOptions.has(TestState.currentQuestionIndex)) {
        TestState.eliminatedOptions.get(TestState.currentQuestionIndex).delete(letter);
      }
    },

    updateEliminationState() {
      const currentEliminated = TestState.eliminatedOptions.get(TestState.currentQuestionIndex) || new Set();

      document.querySelectorAll('.option-row').forEach(optionRow => {
        const optionItem = optionRow.querySelector('.option-item');
        const letterElement = optionRow.querySelector('.external-elimination-letter');

        if (letterElement) {
          const letter = letterElement.getAttribute('data-letter');
          if (currentEliminated.has(letter)) {
            optionItem.classList.add('eliminated');
            letterElement.classList.add('eliminated');
            letterElement.style.background = '#059669';
          } else {
            optionItem.classList.remove('eliminated');
            letterElement.classList.remove('eliminated');
            letterElement.style.background = '#dc2626';
          }
        }
      });
    }
  };

  const SubmissionSystem = {
    modalSubmissionInProgress: false,
    isModalOpen: false,

    validateSubmission() {
      const unanswered = [];
      for (let i = 0; i < TestConfig.totalQuestions; i++) {
        if (!TestState.answeredQuestions.has(i)) {
          unanswered.push(i + 1);
        }
      }
      return {
        hasUnanswered: unanswered.length > 0,
        unansweredQuestions: unanswered,
        count: unanswered.length
      };
    },

    showWarningModal(unansweredCount, unansweredQuestions) {
      if (this.modalSubmissionInProgress || this.isModalOpen) return;

      this.isModalOpen = true;
      const modal = document.getElementById('warningModal');
      const countElement = document.getElementById('unansweredCount');
      const questionsList = document.getElementById('questionsList');

      countElement.textContent = unansweredCount;
      questionsList.innerHTML = '';

      const displayQuestions = unansweredQuestions.slice(0, 12);
      displayQuestions.forEach(qNum => {
        const bubble = document.createElement('div');
        bubble.className = 'question-bubble';
        bubble.textContent = qNum;
        bubble.onclick = () => {
          this.closeWarningModal();
          NavigationSystem.goToQuestion(qNum - 1);
        };
        questionsList.appendChild(bubble);
      });

      if (unansweredQuestions.length > 12) {
        const moreText = document.createElement('div');
        moreText.style.cssText = 'color:#6b7280;font-size:14px;margin-top:8px;';
        moreText.textContent = `and ${unansweredQuestions.length - 12} more`;
        questionsList.appendChild(moreText);
      }

      modal.style.display = 'flex';
      setTimeout(() => {
        modal.querySelector('.warning-modal').classList.add('show');
      }, 50);
    },

    closeWarningModal() {
      if (this.modalSubmissionInProgress) return;

      this.isModalOpen = false;
      const modal = document.getElementById('warningModal');
      const modalContent = modal.querySelector('.warning-modal');

      modalContent.classList.remove('show');
      setTimeout(() => {
        modal.style.display = 'none';
        document.querySelectorAll('.question-bar-btn').forEach(btn => {
          btn.style.animation = '';
        });
      }, 300);
    },

    submitPart() {
      if (this.modalSubmissionInProgress || this.isModalOpen) return;

      const validation = this.validateSubmission();
      if (validation.hasUnanswered) {
        this.showWarningModal(validation.count, validation.unansweredQuestions);
        return;
      }

      this.proceedWithSubmission();
    },

    proceedWithSubmission() {
      if (this.modalSubmissionInProgress) return;
      this.modalSubmissionInProgress = true;

      fetch(TestConfig.URLs.SUBMIT_TEST, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          student_test_id: TestConfig.studentTestId,
          remaining_seconds: TestConfig.Timer.remaining,
          part: TestConfig.currentPart
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success && data.redirect) {
            window.location.href = data.redirect;
          } else {
            throw new Error(data.error || 'Unknown error');
          }
        })
        .catch(error => {
          console.error('Error submitting test:', error);
          window.location.href = `/dashboard/users/tests/${TestConfig.TEST_ID}/results`;
        });
    }
  };

  const CalculatorSystem = {
    desmosCalc: null,
    calculatorInitialized: false,
    DESMOS_FALLBACK_MS: 2000,

    init() {
      this.setupEventListeners();
    },

    setupEventListeners() {
      const btnOpen = document.getElementById('btnCalc');
      const btnClose = document.getElementById('btnCloseCalc');
      const btnToggleKeypad = document.getElementById('btnToggleKeypad');
      const btnExpandCalc = document.getElementById('btnExpandCalc');

      if (btnOpen) btnOpen.addEventListener('click', () => this.open());
      if (btnClose) btnClose.addEventListener('click', () => this.close());
      if (btnToggleKeypad) btnToggleKeypad.addEventListener('click', () => this.toggleKeypad());
      if (btnExpandCalc) btnExpandCalc.addEventListener('click', () => this.toggleExpand());
    },

    open() {
      const pane = document.getElementById('calcPane');
      const workspace = document.getElementById('workspace');

      if (pane && workspace) {
        pane.classList.add('show');
        workspace.classList.remove('no-calc');
        workspace.classList.add('with-calc');

        if (!this.calculatorInitialized) {
          this.initialize();
          setTimeout(() => {
            if (!this.calculatorInitialized || !window.Desmos) {
              this.fallback();
            }
          }, this.DESMOS_FALLBACK_MS);
        } else {
          setTimeout(() => {
            if (this.desmosCalc) this.desmosCalc.resize();
          }, 100);
        }
      }
    },

    close() {
      const pane = document.getElementById('calcPane');
      const workspace = document.getElementById('workspace');

      if (pane && workspace) {
        pane.classList.remove('show');
        workspace.classList.remove('with-calc');
        workspace.classList.add('no-calc');
      }
    },

    initialize() {
      if (this.calculatorInitialized) return;

      const calculatorElement = document.getElementById('desmos');
      if (!calculatorElement || !window.Desmos) return;

      try {
        calculatorElement.innerHTML = '';
        this.desmosCalc = Desmos.GraphingCalculator(calculatorElement, {
          keypad: false,
          expressions: true,
          settingsMenu: true,
          expressionsCollapsed: true
        });

        this.calculatorInitialized = true;

        setTimeout(() => {
          if (this.desmosCalc) this.desmosCalc.resize();
        }, 100);

      } catch (error) {
        console.error('Failed to initialize Desmos calculator:', error);
      }
    },

    fallback() {
      const el = document.getElementById('desmos');
      if (!el || el.__iframeMounted) return;

      try {
        el.innerHTML = '';
        const f = document.createElement('iframe');
        f.src = 'https://www.desmos.com/calculator?embed&lang=en';
        f.title = 'Desmos Calculator';
        f.allow = 'fullscreen';
        f.className = 'calc-iframe';
        f.style.width = '100%';
        f.style.height = '100%';
        f.style.border = '0';
        el.appendChild(f);
        el.__iframeMounted = true;
      } catch(e) {
        console.error('Desmos iframe fallback error', e);
      }
    },

    toggleKeypad() {
      if (!this.desmosCalc) return;
      const keypadVisible = this.desmosCalc.settings.keypad;
      this.desmosCalc.updateSettings({ keypad: !keypadVisible });

      const btn = document.getElementById('btnToggleKeypad');
      btn.textContent = keypadVisible ? '⌨️ Show Keypad' : '⌨️ Hide Keypad';
    },

    toggleExpand() {
      const calcBody = document.getElementById('calcBody');
      if (!calcBody) return;

      const isExpanded = calcBody.classList.contains('expanded');
      calcBody.classList.toggle('expanded', !isExpanded);

      const btn = document.getElementById('btnExpandCalc');
      btn.textContent = isExpanded ? '↕️ Expand' : '↕️ Collapse';

      setTimeout(() => {
        if (this.desmosCalc) this.desmosCalc.resize();
      }, 300);
    }
  };

  const ReferenceSystem = {
    init() {
      const refBtn = document.getElementById('btnRef');
      const refModal = document.getElementById('refBackdrop');
      const refClose = document.getElementById('refClose');

      if (refBtn && refModal && refClose) {
        refBtn.onclick = () => refModal.style.display = 'flex';
        refClose.onclick = () => refModal.style.display = 'none';
        refModal.addEventListener('click', e => {
          if(e.target === refModal) refModal.style.display = 'none';
        });
      }
    }
  };

  const MarkSystem = {
    init() {
      const btnMark = document.getElementById('btnMark');
      const markedField = document.getElementById('markedField');

      if(btnMark && markedField) {
        const syncMark = () => {
          const isMarked = markedField.value === '1';
          btnMark.classList.toggle('active', isMarked);
          btnMark.innerHTML = isMarked ? '🔖 Marked' : '🔖 Mark for Review';

          if (isMarked) {
            TestState.markedQuestions.add(TestState.currentQuestionIndex);
          } else {
            TestState.markedQuestions.delete(TestState.currentQuestionIndex);
          }
          NavigationSystem.updateUI();
        };

        btnMark.addEventListener('click', () => {
          markedField.value = markedField.value === '1' ? '0' : '1';
          syncMark();
        });

        syncMark();
      }
    }
  };

  // إضافة نظام معالجة الصور
  const ImageSystem = {
    init() {
      this.setupImageErrorHandling();
      this.loadAllImages();
    },

    setupImageErrorHandling() {
      // معالجة أخطاء تحميل الصور
      document.addEventListener('error', (e) => {
        if (e.target.tagName === 'IMG') {
          console.warn('Image failed to load:', e.target.src);
          e.target.style.display = 'none';
        }
      }, true);
    },

    loadAllImages() {
      // تحميل جميع الصور عند بدء التشغيل
      const images = document.querySelectorAll('img');
      images.forEach(img => {
        if (img.complete) {
          this.handleImageLoad(img);
        } else {
          img.addEventListener('load', () => this.handleImageLoad(img));
          img.addEventListener('error', () => this.handleImageError(img));
        }
      });
    },

    handleImageLoad(img) {
      img.style.opacity = '1';
      img.style.transition = 'opacity 0.3s ease';
    },

    handleImageError(img) {
      console.warn('Failed to load image:', img.src);
      img.style.display = 'none';
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    if (TestConfig.totalQuestions === 0) {
      SubmissionSystem.proceedWithSubmission();
      return;
    }

    TimerSystem.init();
    EliminationSystem.setup();
    CalculatorSystem.init();
    ReferenceSystem.init();
    MarkSystem.init();
    ImageSystem.init(); // إضافة نظام الصور

    document.getElementById('pauseTimerBtn').addEventListener('click', () => TimerSystem.pause());
    document.getElementById('resumeTimerBtn').addEventListener('click', () => TimerSystem.resume());
    document.getElementById('btnABC').addEventListener('click', () => EliminationSystem.toggleMode());
    document.getElementById('submitAnywayBtn').addEventListener('click', () => SubmissionSystem.proceedWithSubmission());

    document.addEventListener('visibilitychange', () => {
      if (document.hidden && !TestConfig.Timer.isPaused) {
        TimerSystem.pause();
      } else if (!document.hidden && TestConfig.Timer.isPaused === true) {
        // Auto resume if needed
      }
    });

    window.addEventListener('resize', function() {
      if (CalculatorSystem.desmosCalc && CalculatorSystem.calculatorInitialized) {
        setTimeout(() => CalculatorSystem.desmosCalc.resize(), 150);
      }
    });

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        SubmissionSystem.closeWarningModal();
      }
    });

    NavigationSystem.goToQuestion(0);
  });

  function goToQuestion(i) { NavigationSystem.goToQuestion(i); }
  function nextQuestion() { NavigationSystem.nextQuestion(); }
  function previousQuestion() { NavigationSystem.previousQuestion(); }
  function selectMCQOption(el, qid) { AnswerSystem.selectMCQOption(el, qid); }
  function selectTFOption(el, qid) { AnswerSystem.selectTFOption(el, qid); }
  function saveNumericAnswer(el, qid) { AnswerSystem.saveNumericAnswer(el, qid); }
  function submitPart() { SubmissionSystem.submitPart(); }
  function closeWarningModal() { SubmissionSystem.closeWarningModal(); }
</script>
</body>
</html>