<div class="d-flex gap-1">
    <a href="{{ route('dashboard.admins.lectures-assignments') }}?id={{ encrypt($row->id) }}" class="btn btn-info btn-sm" title="@lang('l.Assignments')">
        <i class="fa fa-tasks ti-xs"></i>
    </a>

    <a href="{{ route('dashboard.admins.lectures-edit') }}?id={{ encrypt($row->id) }}" class="btn btn-primary btn-sm" title="@lang('l.Edit')">
        <i class="fa fa-edit ti-xs"></i>
    </a>

    <a href="{{ route('dashboard.admins.lectures-delete') }}?id={{ encrypt($row->id) }}" class="btn btn-danger btn-sm delete-record" title="@lang('l.Delete')">
        <i class="fa fa-trash ti-xs"></i>
    </a>
</div>
