<div class="d-flex gap-2">
    @can('show users')
        <a href="{{ route('dashboard.admins.users-show', ['id' => encrypt($row->id)]) }}"
           class="btn btn-sm btn-icon btn-info"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           title="{{ __('l.Show') }}">
            <i class="fa fa-eye"></i>
        </a>
    @endcan

    @can('edit users')
        @if(($row->id == 1 || $row->id == 2) && auth()->user()->id == $row->id || ($row->id != 1 && $row->id != 2))
            <a href="{{ route('dashboard.admins.users-edit', ['id' => encrypt($row->id)]) }}"
                class="btn btn-sm btn-icon btn-warning"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('l.Edit') }}">
                <i class="fa fa-pencil"></i>
            </a>
        @endif
    @endcan

    @if ($row->id != 1 && $row->id != 2)
        @if(auth()->user()->id != $row->id)
            @can('edit users')
                <a href="{{ route('impersonate', $row->id) }}"
                    class="btn btn-sm btn-icon btn-success"
                    data-bs-toggle="tooltip"
                    title="{{ __('l.Login as') }} {{ $row->firstname }}">
                    <i class="fas fa-door-open"></i>
                </a>
            @endcan

            @can('delete users')
                <a href="{{ route('dashboard.admins.users-delete-inactive', ['id' => encrypt($row->id)]) }}"
                    class="btn btn-sm btn-icon btn-danger delete-record"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="{{ __('l.Delete') }}"
                    data-inactive="false">
                    <i class="fa fa-trash"></i>
                </a>
            @endcan
        @endif
    @endif
</div>