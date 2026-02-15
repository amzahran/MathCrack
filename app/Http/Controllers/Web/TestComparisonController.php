<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\StudentTest;
use Illuminate\Support\Facades\Auth;

class TestComparisonController extends Controller
{
    public function show($testId)
    {
        $user = Auth::user();

        $test = Test::with([
            'questions' => function($query) {
                $query->with('options')
                      ->orderBy('part')
                      ->orderBy('question_order');
            }
        ])->findOrFail($testId); 

        $attempts = $test->attempts()
            ->where('student_id', $user->id)
            ->with([
                'answers' => function ($q) {
                    $q->select(
                        'id',
                        'student_test_id',
                        'test_question_id',
                        'is_correct',
                        'score_earned',
                        'selected_option_id',
                        'answer_text'
                    );
                },
                'answers.question',
            ])
            ->orderBy('created_at', 'asc')
            ->take(3)
            ->get();

        if ($attempts->count() < 2) {
            return back()->with('error', 'At least two attempts are required to compare.');
        }

        $questions = $test->questions;

        $answerIndex = [];
        foreach ($attempts as $a) {
            foreach ($a->answers as $ans) {
                $qid = $ans->test_question_id ?? null;
                if ($qid) {
                    $answerIndex[$a->id][$qid] = $ans;
                }
            }
        }

        $correctOf = function ($ans) {
            if (!$ans) return null;
            $c = $ans->is_correct;
            if (is_null($c) && isset($ans->score_earned)) {
                $c = ((float)$ans->score_earned > 0);
            }
            return $c;
        };

        $progressByQuestion = [];
        foreach ($questions as $q) {
            $a1 = $answerIndex[$attempts[0]->id][$q->id] ?? null;
            $a2 = $answerIndex[$attempts[1]->id][$q->id] ?? null;

            $c1 = $correctOf($a1);
            $c2 = $correctOf($a2);

            $label = 'Not Attempted';
            $tone  = 'secondary';

            if (!is_null($c1) && !is_null($c2)) {
                if ($c1 === false && $c2 === true)       { $label = 'Improved'; $tone = 'success'; }
                elseif ($c1 === true && $c2 === false)   { $label = 'Regressed'; $tone = 'danger'; }
                elseif ($c1 === true && $c2 === true)    { $label = 'Unchanged (correct)'; $tone = 'primary'; }
                elseif ($c1 === false && $c2 === false)  { $label = 'Unchanged (incorrect)'; $tone = 'warning'; }
            } elseif (!is_null($c1) || !is_null($c2)) {
                $label = 'Partially Attempted'; $tone = 'info';
            }

            $progressByQuestion[$q->id] = ['label' => $label, 'tone' => $tone];
        }

        $counters = [
            'a0' => ['correct' => 0, 'incorrect' => 0, 'not_attempted' => 0],
            'a1' => ['correct' => 0, 'incorrect' => 0, 'not_attempted' => 0],
        ];

        foreach ($questions as $q) {
            $a1 = $answerIndex[$attempts[0]->id][$q->id] ?? null;
            $a2 = $answerIndex[$attempts[1]->id][$q->id] ?? null;

            $c1 = $correctOf($a1);
            $c2 = $correctOf($a2);

            if ($c1 === true)      $counters['a0']['correct']++;
            elseif ($c1 === false) $counters['a0']['incorrect']++;
            else                   $counters['a0']['not_attempted']++;

            if ($c2 === true)      $counters['a1']['correct']++;
            elseif ($c2 === false) $counters['a1']['incorrect']++;
            else                   $counters['a1']['not_attempted']++;
        }

        $stats = $this->cardsStats($attempts, (int) ($test->total_score ?? 0));

        $averageScore = round((float) $attempts->avg('final_score'), 1);
        $averagePercentage = 0;
        if ($test->total_score > 0) {
            $averagePercentage = round(($averageScore / $test->total_score) * 100, 1);
        }

        $stats['total_correct_questions']       = $counters['a1']['correct'];
        $stats['total_incorrect_questions']     = $counters['a1']['incorrect'];
        $stats['total_not_attempted_questions'] = $counters['a1']['not_attempted'];
        $stats['a0_question_counters'] = $counters['a0'];
        $stats['a1_question_counters'] = $counters['a1'];

        $totalQ = $questions->count();
        $improved = 0;
        $unchanged = 0;
        foreach ($progressByQuestion as $p) {
            $lbl = $p['label'] ?? '';
            if ($lbl === 'Improved') $improved++;
            if (str_starts_with($lbl, 'Unchanged')) $unchanged++;
        }
        $stats['improved_questions'] = $improved;
        $stats['improvement_rate']   = $totalQ > 0 ? round(($improved / $totalQ) * 100, 1) : 0;
        $stats['consistency_rate']   = $totalQ > 0 ? round(($unchanged / $totalQ) * 100, 1) : 0;


$modules = collect();
        
if ($questions && $questions->count() > 0) {
    $part1Questions = $questions->where('part', 'part1');
    if ($part1Questions->count() > 0) {
        $modules->push((object)[
            'id' => 1,
            'name' => 'Part 1',
            'questions' => $part1Questions
        ]);
    }
    
    $part2Questions = $questions->where('part', 'part2');
    if ($part2Questions->count() > 0) {
        $modules->push((object)[
            'id' => 2,
            'name' => 'Part 2',
            'questions' => $part2Questions
        ]);
    }
    
    $part3Questions = $questions->where('part', 'part3');
    if ($part3Questions->count() > 0) {
        $modules->push((object)[
            'id' => 3,
            'name' => 'Part 3',
            'questions' => $part3Questions
        ]);
    }
    
    $part4Questions = $questions->where('part', 'part4');
    if ($part4Questions->count() > 0) {
        $modules->push((object)[
            'id' => 4,
            'name' => 'Part 4',
            'questions' => $part4Questions
        ]);
    }
    
    $part5Questions = $questions->where('part', 'part5');
    if ($part5Questions->count() > 0) {
        $modules->push((object)[
            'id' => 5,
            'name' => 'Part 5',
            'questions' => $part5Questions
        ]);
    }
}
        $modulesCount = $modules->count();

        $groupedQuestions = collect();

        if ($modulesCount > 0) {
            foreach ($modules as $moduleIndex => $module) {
                $moduleQuestions = $module->questions ?? collect();
                $questionsCount = $moduleQuestions->count();
                
                if ($questionsCount > 0) {
                    $groupedQuestions->push([
                        'module_index' => $moduleIndex + 1,
                        'module' => $module,
                        'questions' => $moduleQuestions,
                        'questions_count' => $questionsCount
                    ]);
                }
            }
        } else {
            $groupedQuestions->push([
                'module_index' => 1,
                'module' => (object)['name' => 'All Questions'],
                'questions' => $questions,
                'questions_count' => $questions->count()
            ]);
        }

        return view(
            'themes.default.back.users.tests.comparison',
            compact(
                'test',
                'attempts',
                'questions',
                'stats',
                'answerIndex',
                'progressByQuestion',
                'averageScore',
                'averagePercentage',
                'modules',
                'modulesCount',
                'groupedQuestions'
            )
        );
    }

    private function cardsStats($attempts, int $totalScore): array
    {
        $s0 = (float) ($attempts[0]->final_score ?? $attempts[0]->current_score ?? 0);
        $s1 = (float) ($attempts[1]->final_score ?? $attempts[1]->current_score ?? 0);

        $delta = $s1 - $s0;

        $pct0 = $totalScore > 0 ? round(($s0 / $totalScore) * 100, 1) : null;
        $pct1 = $totalScore > 0 ? round(($s1 / $totalScore) * 100, 1) : null;
        $pctD = $totalScore > 0 ? round(($delta / $totalScore) * 100, 1) : null;

        return [
            'a0_score'  => $s0,
            'a1_score'  => $s1,
            'delta'     => $delta,
            'a0_pct'    => $pct0,
            'a1_pct'    => $pct1,
            'delta_pct' => $pctD,
        ];
    }
}