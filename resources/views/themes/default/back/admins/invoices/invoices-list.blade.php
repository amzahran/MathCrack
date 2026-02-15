@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.List') @lang('l.Invoices')
@endsection

@section('css')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e9ecef;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @can('show invoices')
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="invoicesTable" class="table table-striped table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.Student')</th>
                                    <th>@lang('l.Course')</th>
                                    <th>@lang('l.Category')</th>
                                    <th>@lang('l.Type')</th>
                                    <th>@lang('l.Item')</th>
                                    <th>@lang('l.Amount')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Created At')</th>
                                    <th>@lang('l.Actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // DataTable initialization
            var table = $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('dashboard.admins.invoices') }}',
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'student_name',
                    name: 'student_name'
                }, {
                    data: 'course_name',
                    name: 'course_name'
                }, {
                    data: 'category_badge',
                    name: 'category'
                }, {
                    data: 'type_badge',
                    name: 'type'
                }, {
                    data: 'type_value_display',
                    name: 'type_value'
                }, {
                    data: 'amount_formatted',
                    name: 'amount'
                }, {
                    data: 'status_badge',
                    name: 'status'
                }, {
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                order: [
                    [8, 'desc']
                ]
            });
        });
    </script>
@endsection
