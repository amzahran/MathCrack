@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Roles & Permissions')
@endsection

@section('css')
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

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <!-- Role cards -->
        <div class="row g-4">
            @can('show roles')
                @foreach ($roles as $role)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-normal mb-2">@lang('l.Total') {{ $role->users->count() }}
                                        @lang('l.users')</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-end mt-1">
                                    {{-- @can('edit roles') --}}
                                        <div class="role-heading">
                                            <h4 class="mb-1" style="text-transform:capitalize;">{{ $role->name }}</h4>
                                            <a href="{{ route('dashboard.admins.roles-edit') }}?id={{ encrypt($role->id) }}"
                                                class="btn btn-dark"><span>@lang('l.Edit Role')</span></a>
                                        </div>
                                    {{-- @endcan --}}
                                    {{-- @can('delete roles') --}}
                                        <a href="javascript:void(0);" class="text-muted delete-role"
                                            data-role-id="{{ encrypt($role->id) }}">
                                            <i class="fa fa-trash ti-md "></i>
                                        </a>
                                    {{-- @endcan --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endcan
            @can('add roles')
                <div class="col-xl-3 col-lg-4 col-md-5">
                    <div class="card d-flex justify-content-center align-items-center" style="min-height: 150px; max-width: 260px; margin:auto;">
                        <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                            class="btn btn-primary add-new-role">
                            <i class="fa fa-plus me-2"></i> @lang('l.Add New Role')
                        </button>
                    </div>
                </div>
            @endcan
        </div>
        <!--/ Role cards -->

        @can('add roles')
            <!-- Add Role Modal -->
            <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-3 p-md-5">
                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <h3 class="role-title mb-2">@lang('l.Add New Role')</h3>
                                <p class="text-muted">@lang('l.Set role permissions')</p>
                            </div>
                            <!-- Add role form -->
                            <form id="addRoleForm" class="row g-3" method="post"
                                action="{{ route('dashboard.admins.roles-store') }}">@csrf
                                <div class="col-12 mb-4">
                                    <label class="form-label" for="modalRoleName">@lang('l.Role Name')</label>
                                    <input type="text" id="modalRoleName" name="name" class="form-control"
                                        placeholder="@lang('l.Enter a role name')" tabindex="-1" required />
                                </div>
                                <div class="col-12">
                                    <h5>@lang('l.Role Permissions')</h5>
                                    <!-- Permission table -->
                                    <div class="table-responsive">
                                        <table class="table table-flush-spacing">
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap fw-semibold">
                                                        @lang('l.Administrator Access')
                                                        <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Allows a full access to the system"></i>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="selectAll" />
                                                            <label class="form-check-label" for="selectAll">
                                                                @lang('l.Select All')
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach ($groupedPermissions as $groupName => $group)
                                                    <tr>
                                                        <td></td>
                                                        <td class="text-center">@lang('l.Show')</td>
                                                        <td class="text-center">@lang('l.Add')</td>
                                                        <td class="text-center">@lang('l.Edit')</td>
                                                        <td class="text-center">@lang('l.Delete')</td>
                                                        <td class="text-center">@lang('l.Other')</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-nowrap fw-semibold">{{ strtoupper($groupName) }}</td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="show {{ strtolower($groupName) }}"
                                                                    id="show_{{ $groupName }}"
                                                                    name="permissions[]"
                                                                    {{ $group->contains('name', 'show ' . strtolower($groupName)) ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="add {{ strtolower($groupName) }}"
                                                                    id="add_{{ $groupName }}"
                                                                    name="permissions[]"
                                                                    {{ $group->contains('name', 'add ' . strtolower($groupName)) ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="edit {{ strtolower($groupName) }}"
                                                                    id="edit_{{ $groupName }}"
                                                                    name="permissions[]"
                                                                    {{ $group->contains('name', 'edit ' . strtolower($groupName)) ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="delete {{ strtolower($groupName) }}"
                                                                    id="delete_{{ $groupName }}"
                                                                    name="permissions[]"
                                                                    {{ $group->contains('name', 'delete ' . strtolower($groupName)) ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @foreach($group as $permission)
                                                                @php
                                                                    $permissionName = strtolower($permission->name);
                                                                    $isStandardAction = Str::startsWith($permissionName, ['show ', 'add ', 'edit ', 'delete ']);
                                                                @endphp

                                                                @if(!$isStandardAction)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            value="{{ $permission->name }}"
                                                                            id="{{ Str::slug($permission->name) }}"
                                                                            name="permissions[]" />
                                                                        <label class="form-check-label" for="{{ Str::slug($permission->name) }}">
                                                                            {{ ucfirst(str_replace($groupName, '', $permission->name)) }}
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Permission table -->
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-100 mb-3 me-sm-3 me-1">
                                        @lang('l.Create')
                                    </button>
                                    <button type="reset" class="btn btn-secondary w-100" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        @lang('l.Back')
                                    </button>
                                </div>
                            </form>
                            <!--/ Add role form -->
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Add Role Modal -->
        @endcan
    </div>
@endsection


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');

            selectAllCheckbox.addEventListener('change', () => {
                checkboxes.forEach((checkbox) => {
                    if (!checkbox.disabled) {
                        checkbox.checked = selectAllCheckbox.checked;
                    }
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-role', function (e) {
            e.preventDefault();
            const roleId = $(this).data('role-id');

            Swal.fire({
                title: "@lang('l.Are you sure?')",
                text: "@lang('l.You will be delete this forever!')",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "@lang('l.Yes, delete it!')",
                cancelButtonText: "@lang('l.Cancel')",
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                // Support both SweetAlert2 v11+ (isConfirmed) and older versions (value)
                if (result && (result.isConfirmed || result.value === true)) {
                    const url = "{{ route('dashboard.admins.roles-delete') }}?id=" + encodeURIComponent(roleId);
                    window.location.assign(url);
                }
            });
        });
    </script>
@endsection
