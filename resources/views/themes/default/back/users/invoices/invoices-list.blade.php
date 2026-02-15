@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.List') @lang('l.Invoices')
@endsection

@section('css')
    <style>
        .badge {
            color: #fff;
        }
    </style>
@endsection


@section('content')
    <div class="main-content">
        <div class="page-category">
            <div class="content-wrapper">
                <div class="card" id="div1">
                    <div class="card-datatable table-responsive">
                        <table class=" table" id="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.Invoice')</th>
                                    <th>@lang('l.Name')</th>
                                    <th>@lang('l.Course')</th>
                                    <th>@lang('l.Type')</th>
                                    <th>@lang('l.Item')</th>
                                    <th>@lang('l.Amount')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Created At')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="color: #d11c1c;">{{ $order->id }}</a></td>
                                        <td>{{ $order->student->firstname . ' ' . $order->student->lastname }}</td>
                                        <td>{{ $order->course ? $order->course->name : '-' }}</td>
                                        <td>{{ $order->type }}</td>
                                        <td>{{ $order->type_value_display }}</td>
                                        <td>{{ $order->amount }}</td>
                                        <td><span
                                                class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'success' : 'danger') }}">@lang('l.' . ucfirst($order->status)) </span>
                                        </td>
                                        <td>{{ $order->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection


@section('js')
    <script>
        var table = $('#data-table').DataTable({
            ordering: true,
            order: [],
        });

        $('#search-input').keyup(function() {
            table.search($(this).val()).draw();
        });
    </script>
@endsection
