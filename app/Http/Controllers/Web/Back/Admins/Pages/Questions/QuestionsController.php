<?php

namespace App\Http\Controllers\Web\Back\Admins\Pages\Questions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Gate;
use App\Models\Language;
use App\Models\Setting;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class QuestionsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show questions')) {
            return view('themes/default/back.permission-denied');
        }

        $questions = Question::orderByDesc('id')->get();

        return view('themes/default/back.admins.pages.questions.questions-list', [
            'questions' => $questions,
        ]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add questions')) {
            return view('themes/default/back.permission-denied');
        }

        $defaultLanguage = app('cached_data')['settings']['default_language'];

        $question = new Question();
        $question->question = [$defaultLanguage => $request->input('question')];
        $question->answer = [$defaultLanguage => $request->input('answer')];
        $question->save();

        // إذا تم تحديد خيار الترجمة التلقائية
        if ($request->input('auto_translate') == 'on') {
            $request->merge(['id' => encrypt($question->id)]);
            $this->autoTranslate($request);
        }

        return redirect()->back()->with('success', __('l.question added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit questions')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $question = Question::findOrFail($id);

        return view('themes/default/back.admins.pages.questions.questions-edit', [
            'question' => $question,
        ]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit questions')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $question = Question::findOrFail($id);


        $defaultLanguage = app('cached_data')['settings']['default_language'];

        $currentQuestion = is_array($question->question) ? $question->question : [];
        $currentAnswer = is_array($question->answer) ? $question->answer : [];

        $question->question = array_merge($currentQuestion, [$defaultLanguage => $request->input('question')]);
        $question->answer = array_merge($currentAnswer, [$defaultLanguage => $request->input('answer')]);
        $question->save();

        return redirect()->back()->with('success', __('l.question updated successfully'));
    }

    public function getTranslations(Request $request)
    {
        if (!Gate::allows('edit questions')) {
            return view('themes/default/back.permission-denied');
        }

        $question = Question::findOrFail(decrypt($request->id));
        $languages = Language::where('is_active', 1)->get();

        return view('themes/default/back.admins.pages.questions.questions-translations', compact('question', 'languages'));
    }

    public function translate(Request $request)
    {
        if (!Gate::allows('edit questions')) {
            return view('themes/default/back.permission-denied');
        }

        // تحقق من جميع حقول الترجمة
        $validationRules = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'question-')) {
                $validationRules[$key] = 'required|string|max:500';
            }
            if (str_starts_with($key, 'answer-')) {
                $validationRules[$key] = 'required|string|max:2000';
            }
        }

        $request->validate($validationRules);

        $question = Question::findOrFail(decrypt($request->id));
        $currentQuestion = is_array($question->question) ? $question->question : [];
        $currentAnswer = is_array($question->answer) ? $question->answer : [];

        $translationsQuestion = [];
        $translationsAnswer = [];
        foreach ($request->except(['_token', '_method', 'id']) as $key => $value) {
            if (str_starts_with($key, 'question-')) {
                $languageCode = str_replace('question-', '', $key);
                $translationsQuestion[$languageCode] = $value;
            }
            if (str_starts_with($key, 'answer-')) {
                $languageCode = str_replace('answer-', '', $key);
                $translationsAnswer[$languageCode] = $value;
            }
        }

        $question->update([
            'question' => array_merge($currentQuestion, $translationsQuestion),
            'answer' => array_merge($currentAnswer, $translationsAnswer),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    public function autoTranslate(Request $request)
    {
        if (!Gate::allows('edit questions')) {
            return view('themes/default/back.permission-denied');
        }

        $question = Question::findOrFail(decrypt($request->id));
        $currentQuestion = is_array($question->question) ? $question->question : [];
        $currentAnswer = is_array($question->answer) ? $question->answer : [];

        $cachedData = app('cached_data');
        $sourceLanguage = $cachedData['settings']['default_language'];

        $languages = Language::where('is_active', 1)
            ->where('code', '!=', $sourceLanguage)
            ->get();

        $translationsQuestion = [];
        $translationsAnswer = [];
        $translator = new GoogleTranslate();

        foreach ($languages as $language) {
            try {
                // ترجمة السؤال
                $translatedQuestion = $translator
                    ->setSource($sourceLanguage)
                    ->setTarget($language->code)
                    ->translate($question->getTranslation('question', $sourceLanguage));

                // ترجمة الإجابة
                $translatedAnswer = $translator
                    ->setSource($sourceLanguage)
                    ->setTarget($language->code)
                    ->translate($question->getTranslation('answer', $sourceLanguage));

                $translationsQuestion[$language->code] = $translatedQuestion;
                $translationsAnswer[$language->code] = $translatedAnswer;

                // تأخير قصير لتجنب تجاوز حد طلبات Google Translate
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                Log::error('Translation error for language ' . $language->code . ': ' . $e->getMessage());
                continue;
            }
        }

        $question->update([
            'question' => array_merge($currentQuestion, $translationsQuestion),
            'answer' => array_merge($currentAnswer, $translationsAnswer),
        ]);

        return redirect()->back()->with('success', __('l.Translation added successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete questions')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $question = Question::find($id);

        if (!$question) {
            return redirect()->back()->with('error', __('l.question dose not exist'));
        }

        $question->delete();

        return redirect()->back()->with('success', __('l.question deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete questions')) {
            return view('themes/default/back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        $questions = Question::whereIn('id', $ids)->get();

        foreach($questions as $question) {
            $question->delete();
        }

        return redirect()->back()->with('success', __('l.Questions deleted successfully'));
    }
}