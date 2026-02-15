@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Taxes settings')
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bx bx-money-withdraw"></i> @lang('l.Taxes settings')</h5>
                        @can('edit settings')
                            <div class="text-end mb-3">
                                <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addTaxModal">
                                    <i class="bx bx-plus me-1"></i> @lang('l.Add New Tax')
                                </a>
                            </div>
                            <div class="modal fade" id="addTaxModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('l.Add New Tax') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('dashboard.admins.taxes-store') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">{{ __('l.Name') }} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('l.Type') }} <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control form-select" name="type" required>
                                                            <option value="fixed">@lang('l.Fixed')</option>
                                                            <option value="percentage">@lang('l.Percentage')</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-5">
                                                        <label class="form-label">{{ __('l.Rate value') }} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="rate" required>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="form-check form-switch mb-2">
                                                            <input type="hidden" name="is_default" value="0">
                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="is_default" value="1" />
                                                            <label class="form-check-label" for="flexSwitchCheckDefault">@lang('l.Is Default')</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="form-check form-switch mb-2">
                                                            <input type="hidden" name="auto_translate" value="0">
                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="auto_translate" value="1" />
                                                            <label class="form-check-label" for="flexSwitchCheckDefault">@lang('l.Auto Translate')</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">{{ __('l.Cancel') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ __('l.Add') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('l.Name')</th>
                                        <th>@lang('l.Type')</th>
                                        <th>@lang('l.Rate value')</th>
                                        <th>@lang('l.Is Default')</th>
                                        <th>@lang('l.Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($taxes as $tax)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><code>{{ $tax->name }}</code></td>
                                        <td>{{ $tax->type == 'fixed' ? __('l.Fixed') : __('l.Percentage') }}</td>
                                        <td>{{ $tax->rate }}</td>
                                        <td>@if ($tax->is_default == 1) <span class="badge bg-label-success">@lang('l.Yes')</span> @else <span class="badge bg-label-danger">@lang('l.No')</span> @endif</td>
                                        <td>
                                            @can('edit settings')
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('dashboard.admins.taxes-edit', ['id' => encrypt($tax->id)]) }}" class="btn btn-sm btn-warning">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.admins.taxes-get-translations', ['id' => encrypt($tax->id)]) }}" class="btn btn-sm btn-dark">
                                                        <i class="bx bx-globe"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-danger delete-tax" data-id="{{ encrypt($tax->id) }}">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">@lang('l.No taxes found')</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.delete-tax').on('click', function() {
                var taxId = $(this).data('id');

                Swal.fire({
                    title: "{{ __('l.Are you sure?') }}",
                    text: "{{ __('l.You will be delete this forever!') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "{{ $settings['primary_color'] }}",
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('l.Yes, delete it!') }}",
                    cancelButtonText: "{{ __('l.Cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('dashboard.admins.taxes-delete') }}?id=" +
                            taxId;
                    }
                });
            });
        });
    </script>
@endsection