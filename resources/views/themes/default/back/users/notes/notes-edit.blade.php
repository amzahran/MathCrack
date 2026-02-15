@extends('themes.default.layouts.back.master')

@section('title')
    {{ Str::title($note->note) }}
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add Role Modal -->
        <div id="addRoleModal" tabindex="-1" aria-hidden="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h3 class="role-title mb-2">@lang('l.Edit Note')</h3>
                        </div>
                        <!-- Add role form -->
                        <form id="editProductForm" method="post" class="row g-3" enctype="multipart/form-data"
                            action="{{ route('dashboard.users.notes-update') }}"> @csrf @method('patch')

                            <div class="col-12 mb-4">
                                <label class="form-label" for="note">@lang('l.Note')</label>
                                <textarea type="text" id="note" name="note" class="form-control" placeholder="Enter a note" rows="5"
                                    required>{{ $note->note }}</textarea>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label" for="date">@lang('l.Date')</label>
                                <input type="date" id="date" name="date" class="form-control"
                                    value="{{ $note->date }}" required />
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label" for="is_still_active">@lang('l.Keep it always') <i class="fa fa-info-circle" data-bs-toggle="tooltip" title="@lang('l.When selected, the notification will remain visible until you delete it. If not selected, the notification will only appear on its specified date and disappear afterwards.')"></i></label>
                                <input type="hidden" name="is_still_active" value="0"/>
                                <input type="checkbox" id="is_still_active" name="is_still_active" class="form-check-input" value="1" {{ $note->is_still_active == 1 ? 'checked' : '' }}/>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">@lang('l.Update')</button>
                                <a href="{{ route('dashboard.users.notes') }}" class="btn btn-secondary">
                                    @lang('l.Cancel')
                                </a>
                            </div>
                            <input type="hidden" name="id" value="{{ encrypt($note->id) }}" />
                        </form>
                    </div>
                    <!--/ Add role form -->
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection


@section('js')
@endsection
