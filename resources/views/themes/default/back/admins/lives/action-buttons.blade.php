<div class="btn-group" role="group">
    <a href="{{ route('dashboard.admins.lives-edit', ['id' => encrypt($row->id)]) }}"
       class="btn btn-sm btn-primary" title="{{ __('l.Edit') }}">
        <i class="fas fa-edit"></i>
    </a>

    <button type="button" class="btn btn-sm btn-danger" data-id="{{ encrypt($row->id) }}" data-name="{{ $row->name }}"
            onclick="deleteLive(this)"
            title="{{ __('l.Delete') }}">
        <i class="fas fa-trash"></i>
    </button>
</div>

<script>
function deleteLive(button) {
    if (confirm('{{ __("l.Are you sure you want to delete") }} "' + button.getAttribute('data-name') + '"?')) {
        window.location.href = '{{ route("dashboard.admins.lives-delete") }}?id=' + button.getAttribute('data-id');
    }
}
</script>
