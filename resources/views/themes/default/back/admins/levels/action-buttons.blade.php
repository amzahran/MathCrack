<div class="d-flex gap-2">
    @can('edit levels')
        <button type="button"
                class="btn btn-sm btn-icon btn-warning edit-level"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('l.Edit') }}"
                data-id="{{ encrypt($row->id) }}"
                data-name="{{ $row->name }}">
            <i class="fa fa-pencil"></i>
        </button>
    @endcan

    @can('delete levels')
        <button type="button"
                class="btn btn-sm btn-icon btn-danger delete-level"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('l.Delete') }}"
                data-id="{{ encrypt($row->id) }}"
                data-name="{{ $row->name }}">
            <i class="fa fa-trash"></i>
        </button>
    @endcan
</div>
