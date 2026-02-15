@extends('themes.default.layouts.back.master')


@section('title')
    @lang('l.Payments Gateways List')
@endsection

@section('css')

@endsection

@section('content')
    <div class="main-content">

        @can('show settings')
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.Name')</th>
                                    <th>@lang('l.Fees')</th>
                                    <th>@lang('l.Fees Type')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Description')</th>
                                    @can('edit settings')
                                        <th class="text-end">@lang('l.Edit')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments->sortBy('order') as $method)
                                    <tr data-filter-name="{{ strtolower($method->name) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::title($method->name) }}</td>
                                        <td>
                                            @if ($method->fees_type == 'fixed')
                                                {{ $method->fees }} {{ strtoupper($settings['default_currency']) }}
                                            @else
                                                {{ $method->fees }}%
                                            @endif
                                        </td>
                                        <td>{{ Str::title($method->fees_type) }}</td>
                                        <td>
                                            @if ($method->status == '1')
                                                <span class="badge bg-success">@lang('l.Active')</span>
                                            @else
                                                <span class="badge bg-secondary">@lang('l.Inactive')</span>
                                            @endif
                                        </td>
                                        <td class="text-truncate" style="max-width: 320px;" title="{{ $method->description }}">
                                            {{ Str::limit($method->description, 120) }}
                                        </td>
                                        @can('edit settings')
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#model-{{ $method->name }}" title="@lang('l.Edit')">
                                                    <i class="fa fa-edit me-1"></i> @lang('l.Edit')
                                                </button>
                                            </td>
                                        @endcan
                                    </tr>

                                    @can('edit settings')
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="model-{{ $method->name }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role" style="justify-content: center;">
                                                <div class="modal-content p-3 p-md-5 col-md-8">
                                                    <div class="modal-body">
                                                        <div class="text-center mb-4">
                                                            <h3 class="role-title mb-2">@lang('l.Edit') <span style="color:red;">{{ strtoupper($method->name) }}</span></h3>
                                                        </div>
                                                        <form id="addProductForm" class="row g-3" method="post" enctype="multipart/form-data" action="{{ route('dashboard.admins.payments-update') }}">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-4 mb-4">
                                                                    <label class="form-label" for="status">@lang('l.Status')</label>
                                                                    <select id="status" class="form-select" name="status" required data-select2-selector="language">
                                                                        <option value="1" {{ $method->status == 1 ? 'selected' : '' }}>@lang('l.Active')</option>
                                                                        <option value="0" {{ $method->status == 0 ? 'selected' : '' }}>@lang('l.Inactive')</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 mb-4">
                                                                    <label class="form-label" for="fees_type">@lang('l.Fees Type')</label>
                                                                    <select id="fees_type" class="form-select" name="fees_type" required data-select2-selector="language">
                                                                        <option value="percentage" {{ $method->fees_type == 'percentage' ? 'selected' : '' }}>@lang('l.Percentage')</option>
                                                                        <option value="fixed" {{ $method->fees_type == 'fixed' ? 'selected' : '' }}>@lang('l.Fixed')</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 mb-4">
                                                                    <label class="form-label" for="fees">@lang('l.Fees Amount')</label>
                                                                    <input type="text" id="fees" name="fees" class="form-control" value="{{ $method->fees }}" placeholder="@lang('l.Enter a method fees or percentage')" />
                                                                </div>
                                                                <div class="col-12 mb-4">
                                                                    <label class="form-label" for="description">@lang('l.Description')
                                                                        <small class="text-muted">({{ $defaultLanguage->name }} <i class="fi fi-{{ $defaultLanguage->flag }} rounded"></i>)</small>
                                                                    </label>
                                                                    <input type="text" id="description" name="description" class="form-control" value="{{ $method->description }}" placeholder="@lang('l.Enter a method description')" />
                                                                </div>
                                                                @foreach ($method->settings as $setting)
                                                                    <div class="col-12 mb-4">
                                                                        <label class="form-label" for="{{ $setting->key }}">
                                                                            @if ($setting->key == 'CASH_ON_DELIVERY')
                                                                                @lang('l.Cash on Delivery static Fee')
                                                                            @else
                                                                                {{ $setting->key }}
                                                                            @endif
                                                                        </label>
                                                                        <input type="text" id="{{ $setting->key }}" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}" placeholder="@lang('l.Enter a method') {{ $setting->key }}" />
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <input type="hidden" name="id" value="{{ encrypt($method->id) }}">
                                                            <div class="col-12 text-center mt-4">
                                                                <button type="submit" class="btn btn-primary me-sm-3 me-1">@lang('l.Submit')</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
@endsection
