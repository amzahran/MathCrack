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
        <button type="button"
                class="btn btn-sm btn-icon btn-danger delete-course"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('l.Delete') }}"
                data-id="{{ encrypt($row->id) }}"
                data-name="{{ $row->name }}">
            <i class="fa fa-trash"></i>
        </button>
    @endcan
</div>
