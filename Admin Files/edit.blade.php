@extends('themes.default.layouts.back.master')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">
        {{ isset($test) ? __('l.edittest') : __('l.createtest') }}
    </h4>

    <form method="POST"
          action="{{ isset($test)
                ? route('dashboard.admins.tests.update', ['id' => encrypt($test->id)])
                : route('dashboard.admins.tests.store') }}">
        @csrf

        {{-- اسم الاختبار --}}
        <div class="mb-3">
            <label class="form-label">{{ __('l.name') }}</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $test->name ?? '') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- الكورس --}}
        <div class="mb-3">
            <label class="form-label">{{ __('l.course') }}</label>
            <select name="course_id" class="form-select" required>
                <option value="">{{ __('l.select') }}</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ old('course_id', $test->course_id ?? '') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            @error('course_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- الوصف --}}
        <div class="mb-3">
            <label class="form-label">{{ __('l.description') }}</label>
            <textarea name="description" class="form-control" rows="3">
                {{ old('description', $test->description ?? '') }}
            </textarea>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="row">
            {{-- السعر --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('l.price') }}</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control"
                       value="{{ old('price', $test->price ?? 0) }}" required>
                @error('price') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- الدرجة الابتدائية --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('l.initial_score') }}</label>
                <input type="number" name="initial_score" class="form-control"
                       value="{{ old('initial_score', $test->initial_score ?? 0) }}" required>
                @error('initial_score') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- درجة السؤال --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('l.default_question_score') }}</label>
                <input type="number" name="default_question_score" class="form-control"
                       value="{{ old('default_question_score', $test->default_question_score ?? 1) }}" required>
                @error('default_question_score') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row">
            {{-- أقصى عدد محاولات --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('l.max_attempts') }}</label>
                <input type="number" name="max_attempts" class="form-control"
                       value="{{ old('max_attempts', $test->max_attempts ?? 1) }}" required>
                @error('max_attempts') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- وقت الاستراحة --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('l.break_time_minutes') }}</label>
                <input type="number" name="break_time_minutes" class="form-control"
                       value="{{ old('break_time_minutes', $test->break_time_minutes ?? 0) }}">
                @error('break_time_minutes') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- حالة الاختبار --}}
            <div class="col-md-4 mb-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           {{ old('is_active', $test->is_active ?? 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('l.active') }}</label>
                </div>
            </div>
        </div>

        {{-- الموديولات --}}
        <hr>
        <h5>{{ __('l.modules') }}</h5>

        <div id="modules-wrapper">
            @php
                $oldModules = old('modulesdata');
                if ($oldModules === null && isset($test) && $test->modules_data) {
                    $oldModules = $test->modules_data; // من الموديل (cast as array)
                }
                if (is_string($oldModules)) {
                    $decoded = json_decode($oldModules, true);
                    $oldModules = is_array($decoded) ? $decoded : [];
                }
                if (!is_array($oldModules)) {
                    $oldModules = [];
                }
            @endphp

            @if(count($oldModules))
                @foreach($oldModules as $module)
                    <div class="card mb-2 module-item">
                        <div class="card-body row">
                            <div class="col-md-5 mb-2">
                                <label class="form-label">{{ __('l.questionscount') }}</label>
                                <input type="number" class="form-control module-questionscount"
                                       value="{{ $module['questionscount'] ?? 0 }}">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label">{{ __('l.timeminutes') }}</label>
                                <input type="number" class="form-control module-timeminutes"
                                       value="{{ $module['timeminutes'] ?? 0 }}">
                            </div>
                            <div class="col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-remove-module w-100">
                                    {{ __('l.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- موديول افتراضي واحد --}}
                <div class="card mb-2 module-item">
                    <div class="card-body row">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">{{ __('l.questionscount') }}</label>
                            <input type="number" class="form-control module-questionscount" value="0">
                        </div>
                        <div class="col-md-5 mb-2">
                            <label class="form-label">{{ __('l.timeminutes') }}</label>
                            <input type="number" class="form-control module-timeminutes" value="0">
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-module w-100">
                                {{ __('l.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mb-3">
            <button type="button" id="btn-add-module" class="btn btn-secondary">
                {{ __('l.addmodule') }}
            </button>
        </div>

        {{-- hidden field الذي يلتقطه الكنترولر كـ modulesdata (JSON) --}}
        <input type="hidden" name="modulesdata" id="modulesdata_input">
       <p>DEBUG: modulesdata = <span id="modulesdata_debug"></span></p>

        @error('modulesdata')
            <small class="text-danger d-block">{{ $message }}</small>
        @enderror

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($test) ? __('l.savechanges') : __('l.create') }}
            </button>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.querySelector('form');
    const wrapper   = document.getElementById('modules-wrapper');
    const hidden    = document.getElementById('modulesdata_input');
    const debugSpan = document.getElementById('modulesdata_debug');

    if (!form || !wrapper || !hidden) {
        return;
    }

    form.addEventListener('submit', function () {
        const modules = [];
        wrapper.querySelectorAll('.module-item').forEach(function (item) {
            const q = item.querySelector('.module-questionscount');
            const t = item.querySelector('.module-timeminutes');
            const questions = parseInt(q.value || 0, 10);
            const minutes   = parseInt(t.value || 0, 10);
            if (questions > 0 && minutes > 0) {
                modules.push({
                    questionscount: questions,
                    timeminutes: minutes
                });
            }
        });
        hidden.value = JSON.stringify(modules);
        if (debugSpan) {
            debugSpan.textContent = hidden.value;
        }
        console.log('modulesdata:', hidden.value);
    });
});
</script>
@endpush

