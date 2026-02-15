@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Support Tickets')
@endsection

@section('seo')
@endsection

@section('css')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-content">
        <div class="container-fluid">
            <div class="content-wrapper">
                <h4 class="fw-semibold mb-4">@lang('l.Support Tickets')</h4>
                <div class="card-action-element mb-2 add-new-product" style="text-align: end; ">
                    <a href="#" data-bs-target="#addRoleModal" data-bs-toggle="modal"
                        class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-plus bx-xs me-1"></i>@lang('l.Add New Ticket')
                    </a>
                </div>
                <!-- Add Role Modal -->
                <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role"
                        style="justify-content: center;">
                        <div class="modal-content p-3 p-md-5 col-md-8">
                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <h3 class="role-title mb-2">@lang('l.Add New Ticket')</h3>
                                </div>
                                <!-- Add role form -->
                                <form id="addRoleForm" class="row g-3" method="post"
                                    action="{{ route('dashboard.users.tickets-store') }}">@csrf
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="modalRoleName">@lang('l.Subject')</label>
                                        <input type="text" id="modalRoleName" name="subject" class="form-control"
                                            placeholder="@lang('l.Enter a Subject')" tabindex="-1" required />
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="id">@lang('l.Support Type')</label>
                                        <select id="id" class="select2 form-control" name="support_type" required>
                                            <option value="">@lang('l.Select')</option>
                                            <option value="sales support">@lang('l.Sales support')</option>
                                            <option value="technical support">@lang('l.Technical support')</option>
                                            <option value="Admin">@lang('l.Admin')</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="Whois">@lang('l.Description')</label>
                                        <textarea id="Whois" name="description" class="form-control" required rows="10"></textarea>
                                    </div>
                                    <div class="col-12 text-center mt-4">
                                        <button type="submit" class="btn btn-primary me-sm-3 me-1">@lang('l.Submit')</button>
                                    </div>
                                </form>
                                <!--/ Add role form -->
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Add Role Modal -->

                <div class="card" style="padding: 15px;">
                    <div class="card-datatable table-responsive">
                        <table class="table" id="tickets-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.Subject')</th>
                                    <th>@lang('l.Support Type')</th>
                                    <th>@lang('l.Description')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="capital">{{ Str::limit($ticket->subject, 18) }}</td>
                                        <td class="capital">{{ __('l.' . ucfirst($ticket->support_type)) }}</td>
                                        <td class="capital">{{ Str::limit($ticket->description, 45) }}</td>
                                        <td class="capital">
                                            @if ($ticket->status == 'answered')
                                                <span class="badge bg-success">@lang('l.Answered')</span>
                                            @elseif($ticket->status == 'in_progress')
                                                <span class="badge bg-danger">@lang('l.In Progress')</span>
                                            @elseif($ticket->status == 'closed')
                                                <span class="badge bg-dark">@lang('l.Closed')</span>
                                            @endif
                                        </td>
                                        <td class="capital">
                                            @if ($ticket->status == 'closed')
                                                <button class="btn rounded-pill btn-label-secondary waves-effect"
                                                    disabled><i class="bx bx-show bx-xs"></i></button>
                                            @else
                                                <a href="{{ route('dashboard.users.tickets-show') }}?id={{ encrypt($ticket->id) }}"
                                                    data-bs-toggle="tooltip" title="@lang('l.Show')"
                                                    class="btn rounded-pill btn-info waves-effect"><i class="bx bx-show bx-xs"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
        var table = $('#tickets-table').DataTable({
            ordering: true,
            order: [],
            searching: false
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addRoleButton = document.querySelector('.add-new-product');
            const addRoleModal = document.querySelector('#addRoleModal');

            addRoleButton.addEventListener('click', function() {
                var modal = new bootstrap.Modal(addRoleModal);
                modal.show();
            });
        });
    </script>
@endsection
