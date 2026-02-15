<?php
// في App\Http\Requests\StoreTestRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // يمكنك تغيير هذا حسب نظام الصلاحيات
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $courseId = $this->input('course_id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(function ($query) use ($courseId) {
                    return $query->where('course_id', $courseId);
                })
            ],
            'description' => 'nullable|string|max:1000',
            'course_id' => 'required|exists:courses,id',
            'price' => 'nullable|numeric|min:0',
            'initial_score' => 'nullable|integer|min:0',
            'default_question_score' => 'nullable|integer|min:0',
            
            // Modules system fields
            'modules_data' => 'sometimes|array',
            'modules_data.*.name' => 'required_with:modules_data|string|max:255',
            'modules_data.*.questions_count' => 'required_with:modules_data|integer|min:1',
            'modules_data.*.time_minutes' => 'required_with:modules_data|integer|min:1',
            
            // Legacy fields - nullable when using modules
            'part1_questions_count' => 'nullable|integer|min:0',
            'part1_time_minutes' => 'nullable|integer|min:0',
            'part2_questions_count' => 'nullable|integer|min:0',
            'part2_time_minutes' => 'nullable|integer|min:0',
            
            'break_time_minutes' => 'nullable|integer|min:0',
            'max_attempts' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Test name is required',
            'name.unique' => 'A test with this name already exists in the selected course',
            'course_id.required' => 'Please select a course',
            'course_id.exists' => 'The selected course does not exist',
            'modules_data.*.name.required_with' => 'Module name is required',
            'modules_data.*.questions_count.required_with' => 'Questions count is required for each module',
            'modules_data.*.questions_count.min' => 'Questions count must be at least 1',
            'modules_data.*.time_minutes.required_with' => 'Time minutes is required for each module',
            'modules_data.*.time_minutes.min' => 'Time must be at least 1 minute',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تحويل modules_data من JSON string إلى array إذا لزم الأمر
        if ($this->has('modules_data') && is_string($this->modules_data)) {
            $this->merge([
                'modules_data' => json_decode($this->modules_data, true)
            ]);
        }

        // تعيين قيم افتراضية إذا لم يتم تقديمها
        $this->merge([
            'initial_score' => $this->initial_score ?? 0,
            'default_question_score' => $this->default_question_score ?? 1,
            'break_time_minutes' => $this->break_time_minutes ?? 0,
            'max_attempts' => $this->max_attempts ?? 1,
            'is_active' => $this->is_active ?? true,
        ]);
    }
}