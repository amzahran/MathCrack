<div class="d-flex gap-2">
    @can('edit users')

        <form method="POST" action="{{ route('dashboard.admins.users-active', ['id' => encrypt($row->id)]) }}" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="btn btn-sm btn-icon btn-success"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('l.Activate User') }}">
                <i class="fa fa-user-check"></i>
            </button>
        </form>
    @endcan

    @can('delete users')
        <a href="{{ route('dashboard.admins.users-delete-inactive', ['id' => encrypt($row->id)]) }}"
            class="btn btn-sm btn-icon btn-danger delete-record"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           title="{{ __('l.Permanent Delete') }}"
           data-inactive="true">
            <i class="fa fa-trash"></i>
        </a>
    @endcan
</div>
