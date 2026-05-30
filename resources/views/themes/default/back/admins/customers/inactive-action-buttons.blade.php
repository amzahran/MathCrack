<div class="d-flex gap-2">
    @can('edit students')

        <form method="POST" action="{{ route('dashboard.admins.customers-active', ['id' => encrypt($row->id)]) }}" class="d-inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="id" value="{{ encrypt($row->id) }}">
            <button type="submit"
                class="btn btn-sm btn-icon btn-success"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('l.Activate User') }}">
                <i class="fa fa-user-check"></i>
            </button>
        </form>
    @endcan

    @can('delete students')
        <a href="{{ route('dashboard.admins.customers-delete-inactive', ['id' => encrypt($row->id)]) }}"
            class="btn btn-sm btn-icon btn-danger delete-record"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           title="{{ __('l.Permanent Delete') }}"
           data-inactive="true">
            <i class="fa fa-trash"></i>
        </a>
    @endcan
</div>
