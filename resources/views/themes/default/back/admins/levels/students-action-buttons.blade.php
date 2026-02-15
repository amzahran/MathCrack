<div class="d-flex gap-2">
    @can('show students')
        <a href="{{ route('dashboard.admins.customers-show', ['id' => encrypt($row->id)]) }}"
           class="btn btn-sm btn-icon btn-info"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           title="{{ __('l.Show') }}">
            <i class="fa fa-eye"></i>
        </a>
    @endcan

    @can('edit students')
        <a href="{{ route('dashboard.admins.customers-edit', ['id' => encrypt($row->id)]) }}"
            class="btn btn-sm btn-icon btn-warning"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('l.Edit') }}">
            <i class="fa fa-pencil"></i>
        </a>
    @endcan

    @can('delete students')
        <a href="{{ route('dashboard.admins.customers-inactive', ['id' => encrypt($row->id)]) }}"
            class="btn btn-sm btn-icon btn-danger delete-record"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('l.Delete') }}">
            <i class="fa fa-trash"></i>
        </a>
    @endcan
</div>
