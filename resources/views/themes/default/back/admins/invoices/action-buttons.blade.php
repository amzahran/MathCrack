<div class="d-flex gap-2">
    @can('show invoices')
        <a href="{{ route('dashboard.admins.invoices-show', ['id' => encrypt($row->id)]) }}"
           class="btn btn-sm btn-info"
           title="@lang('l.View')">
            <i class="fas fa-eye"></i>
        </a>
    @endcan

    @can('delete invoices')
        <form method="POST"
              action="{{ route('dashboard.admins.invoices-delete') }}"
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this invoice? This may affect access records.')">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id" value="{{ encrypt($row->id) }}">
            <button type="submit"
                    class="btn btn-sm btn-danger"
                    title="@lang('l.Delete')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
