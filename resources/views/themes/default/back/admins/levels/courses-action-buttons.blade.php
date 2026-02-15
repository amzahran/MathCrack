<div class="d-flex gap-2">
    @can('edit courses')
        <a href="{{ route('dashboard.admins.courses-edit', ['id' => encrypt($row->id)]) }}"
            class="btn btn-sm btn-icon btn-warning"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('l.Edit') }}">
            <i class="fa fa-pencil"></i>
        </a>
    @endcan

    @can('delete courses')
        <a href="{{ route('dashboard.admins.courses-delete', ['id' => encrypt($row->id)]) }}"
            class="btn btn-sm btn-icon btn-danger delete-record"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('l.Delete') }}">
            <i class="fa fa-trash"></i>
        </a>
    @endcan
</div>
