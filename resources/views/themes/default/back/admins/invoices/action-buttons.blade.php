<div class="d-flex gap-2">
    @can('show invoices')
        <a href="{{ route('dashboard.admins.invoices-show', ['id' => encrypt($row->id)]) }}"
           class="btn btn-sm btn-info"
           title="@lang('l.View')">
            <i class="fas fa-eye"></i>
        </a>
    @endcan

    @can('delete invoices')
        <a href="{{ route('dashboard.admins.invoices-delete', ['id' => encrypt($row->id)]) }}"
           class="btn btn-sm btn-danger"
           title="@lang('l.Delete')"
           onclick="return confirm('@lang('l.Are you sure you want to delete this invoice?')')">
            <i class="fas fa-trash"></i>
        </a>
    @endcan
</div>
