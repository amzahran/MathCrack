<div class="question-header">
    <div class="d-flex align-items-center">
        <span class="question-number">{{ $index + 1 }}</span>
        <select class="form-select question-type-select" onchange="handleQuestionTypeChange('{{ $question->id }}', this.value)">
            <option value="mcq" {{ $question->type === 'mcq' ? 'selected' : '' }}>@lang('l.Multiple Choice')</option>
            <option value="tf" {{ $question->type === 'tf' ? 'selected' : '' }}>@lang('l.true_false')</option>
            <option value="essay" {{ $question->type === 'essay' ? 'selected' : '' }}>@lang('l.Essay')</option>
            <option value="numeric" {{ $question->type === 'numeric' ? 'selected' : '' }}>@lang('l.Numeric')</option>
        </select>
        @php
            $badgeClass = match($question->type) {
                'mcq' => 'mcq-badge',
                'tf' => 'tf-badge',
                'essay' => 'essay-badge',
                'numeric' => 'numeric-badge',
                default => 'mcq-badge'
            };
            $badgeText = match($question->type) {
                'mcq' => __('l.Multiple Choice'),
                'tf' => __('l.true_false'),
                'essay' => __('l.Essay'),
                'numeric' => __('l.Numeric'),
                default => __('l.Multiple Choice')
            };
        @endphp
        <span class="question-type-badge {{ $badgeClass }} ms-2">{{ $badgeText }}</span>
    </div>
    <div class="action-buttons">
        <button class="btn btn-icon btn-delete" onclick="deleteQuestion('{{ $question->id }}')" title="@lang('l.delete')">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>

<div class="question-content">
    <div class="row">
        <div class="col-md-8">
            <label class="form-label fw-bold">@lang('l.question_text'):</label>
            <textarea class="form-control question-text-editor"
                    placeholder="@lang('l.question_text_placeholder')"
                    onblur="renderMath(this)">{{ $question->question_text }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">@lang('l.question_image_optional'):</label>
            <input type="file" class="form-control question-image" accept="image/*">
            @if($question->question_image)
                <div class="mt-2">
                    <img src="{{ asset($question->question_image) }}"
                         alt="@lang('l.question_image_optional')" class="img-thumbnail" style="max-height: 100px;">
                    <small class="d-block text-muted">@lang('l.current_image')</small>
                </div>
            @endif
            <label class="form-label fw-bold mt-2">@lang('l.explanation_image_optional'):</label>
            <input type="file" class="form-control explanation-image" accept="image/*">
            @if($question->explanation_image)
                <div class="mt-2">
                    <img src="{{ asset($question->explanation_image) }}"
                         alt="@lang('l.explanation_image_optional')" class="img-thumbnail" style="max-height: 100px;">
                    <small class="d-block text-muted">@lang('l.current_image')</small>
                </div>
            @endif
            <div class="mt-2">
                <label class="form-label fw-bold">@lang('l.points'):</label>
                <input type="number" class="form-control question-points" min="1" value="{{ $question->points }}">
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <label class="form-label fw-bold">@lang('l.answer_explanation_optional'):</label>
            <textarea class="form-control question-explanation" rows="2"
                    placeholder="@lang('l.answer_explanation_placeholder')">{{ $question->explanation }}</textarea>
        </div>
    </div>
</div>

<div class="options-container" id="options-{{ $question->id }}">
    @if($question->type === 'mcq')
        <label class="form-label fw-bold">@lang('l.options'):</label>
        <div class="mcq-options">
            @foreach($question->options()->orderBy('order')->get() as $optionIndex => $option)
                <div class="option-item {{ $option->is_correct ? 'correct-answer' : '' }}" data-option-index="{{ $optionIndex }}">
                    <div class="option-header">
                        <span class="option-letter {{ $option->is_correct ? 'correct' : '' }}">{{ chr(65 + $optionIndex) }}</span>
                        <input type="radio" name="correct-{{ $question->id }}" value="{{ $optionIndex }}"
                               class="form-check-input ms-2" {{ $option->is_correct ? 'checked' : '' }}>
                        <span class="ms-2 small text-muted">@lang('l.correct_answer')</span>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMCQOption(this)"
                                    {{ $question->options->count() <= 2 ? 'style="display:none"' : '' }}>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="option-content">
                        <textarea class="form-control option-text-editor"
                                placeholder="@lang('l.option_text_placeholder')"
                                onblur="renderMath(this)">{{ $option->option_text }}</textarea>
                        <input type="file" class="form-control mt-2" accept="image/*" placeholder="@lang('l.option_image_optional')">
                        @if($option->option_image)
                            <div class="mt-2">
                                <img src="{{ asset($option->option_image) }}"
                                     alt="@lang('l.option_image_optional')" class="img-thumbnail" style="max-height: 60px;">
                                <small class="d-block text-muted">@lang('l.current_image')</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMCQOption('{{ $question->id }}')">
            <i class="fas fa-plus me-1"></i> @lang('l.add_option')
        </button>
    @elseif($question->type === 'tf')
        <label class="form-label fw-bold">@lang('l.options'):</label>
        <div class="tf-options">
            <div class="option-item {{ $question->correct_answer === 'true' ? 'correct-answer' : '' }}" data-option-index="0">
                <div class="option-header">
                    <span class="option-letter {{ $question->correct_answer === 'true' ? 'correct' : '' }}">✓</span>
                    <input type="radio" name="correct-{{ $question->id }}" value="0"
                           class="form-check-input ms-2" {{ $question->correct_answer === 'true' ? 'checked' : '' }}>
                    <span class="ms-2 fw-bold text-success">@lang('l.true')</span>
                </div>
            </div>
            <div class="option-item {{ $question->correct_answer === 'false' ? 'correct-answer' : '' }}" data-option-index="1">
                <div class="option-header">
                    <span class="option-letter {{ $question->correct_answer === 'false' ? 'correct' : '' }}">✗</span>
                    <input type="radio" name="correct-{{ $question->id }}" value="1"
                           class="form-check-input ms-2" {{ $question->correct_answer === 'false' ? 'checked' : '' }}>
                    <span class="ms-2 fw-bold text-danger">@lang('l.false')</span>
                </div>
            </div>
        </div>
    @elseif($question->type === 'essay')
        <label class="form-label fw-bold">@lang('l.model_answer'):</label>
        <div class="essay-answer-area">
            <textarea class="form-control" rows="4"
                    placeholder="@lang('l.model_answer_placeholder')">{{ $question->correct_answer }}</textarea>
        </div>
    @elseif($question->type === 'numeric')
        <label class="form-label fw-bold">@lang('l.correct_numeric_answer'):</label>
        <input type="number" class="form-control numeric-answer-input"
               placeholder="@lang('l.enter_correct_number')" step="any" value="{{ $question->correct_answer }}">
        <small class="text-muted mt-1">@lang('l.decimal_numbers_allowed')</small>
    @endif
</div>

<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
    <div class="question-stats">
        <span><i class="fas fa-calendar-alt"></i> @lang('l.created_at'): {{ $question->created_at->format('Y-m-d H:i') }}</span>
        <span><i class="fas fa-edit"></i> @lang('l.last_updated'): {{ $question->updated_at->format('Y-m-d H:i') }}</span>
        @if($question->type === 'mcq')
            <span><i class="fas fa-list"></i> {{ $question->options->count() }} @lang('l.options_count')</span>
        @endif
    </div>
    <button class="btn btn-success" onclick="saveQuestion('{{ $question->id }}')">@lang('l.save')</button>
</div>