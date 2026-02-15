@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Edit Role')
@endsection

@section('css')
@endsection


@section('content')
    <div class="main-content">
        @can('edit roles')
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

            <!-- Add Role Modal -->
            <div id="addRoleModal" tabindex="-1" aria-hidden="false">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-3 p-md-5">
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <h3 class="role-title mb-2">@lang('l.Edit') <span
                                        style="color: red;">{{ $role->name }}</span></h3>
                                <p class="text-muted">@lang('l.Set role permissions')</p>
                            </div>
                            <!-- Add role form -->
                            <form id="addRoleForm" class="row g-3" method="post"
                                action="{{ route('dashboard.admins.roles-update') }}">@csrf
                                <div class="col-12 mb-4">
                                    <label class="form-label" for="modalRoleName">@lang('l.Role Name')</label>
                                    <input type="text" id="modalRoleName" name="name" value="{{ $role->name }}"
                                        class="form-control" placeholder="@lang('l.Enter a role name')" tabindex="-1" />
                                </div>
                                <div class="col-12">
                                    <h3 class="fw-semibold">@lang('l.Role Permissions')</h3>
                                    <!-- Permission table -->
                                    <div class="table-responsive">
                                        <table class="table table-flush-spacing">
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap fw-semibold">
                                                        @lang('l.Administrator Access')
                                                        <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="@lang('l.Allows a full access to the system')"></i>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="selectAll" />
                                                            <label class="form-check-label" for="selectAll"> @lang('l.Select All')
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
                                                                @php
                                                                    $permissionName = 'show ' . strtolower($groupName);
                                                                    $hasPermission = $group->contains('name', $permissionName);
                                                                @endphp
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="{{ $permissionName }}"
                                                                    id="show_{{ $groupName }}" name="permissions[]"
                                                                    @if ($hasPermission && $role->hasPermissionTo($permissionName)) checked @endif
                                                                    {{ $hasPermission ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                @php
                                                                    $permissionName = 'add ' . strtolower($groupName);
                                                                    $hasPermission = $group->contains('name', $permissionName);
                                                                @endphp
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="{{ $permissionName }}"
                                                                    id="add_{{ $groupName }}" name="permissions[]"
                                                                    @if ($hasPermission && $role->hasPermissionTo($permissionName)) checked @endif
                                                                    {{ $hasPermission ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                @php
                                                                    $permissionName = 'edit ' . strtolower($groupName);
                                                                    $hasPermission = $group->contains('name', $permissionName);
                                                                @endphp
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="{{ $permissionName }}"
                                                                    id="edit_{{ $groupName }}" name="permissions[]"
                                                                    @if ($hasPermission && $role->hasPermissionTo($permissionName)) checked @endif
                                                                    {{ $hasPermission ? '' : 'disabled' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                @php
                                                                    $permissionName = 'delete ' . strtolower($groupName);
                                                                    $hasPermission = $group->contains('name', $permissionName);
                                                                @endphp
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="{{ $permissionName }}"
                                                                    id="delete_{{ $groupName }}" name="permissions[]"
                                                                    @if ($hasPermission && $role->hasPermissionTo($permissionName)) checked @endif
                                                                    {{ $hasPermission ? '' : 'disabled' }} />
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
                                                                            name="permissions[]"
                                                                            @if ($role->hasPermissionTo($permission->name)) checked @endif />
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
                                <input type="hidden" name="id" value="{{ encrypt($role->id) }}">
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-100 mb-3 me-sm-3 me-1">@lang('l.Submit')</button>
                                    <a href="{{ route('dashboard.admins.roles') }}" class="btn btn-secondary">
                                        @lang('l.Cancel')
                                    </a>
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
@endsection
