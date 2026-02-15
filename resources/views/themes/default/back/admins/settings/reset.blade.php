<div class="tab-pane fade show active" id="v-pills-General" role="tabpanel" aria-labelledby="v-pills-General-tab">
    <div class="card border-danger shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-white">
                <i class="fas fa-exclamation-triangle me-2 fa-fw"></i>
                @lang('l.Reset System')
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-warning shadow-sm" role="alert">
                <h4 class="alert-heading mb-3 text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @lang('l.Warning: Dangerous Operation!')
                </h4>
                <p class="fw-bold">@lang('l.This operation will permanently delete all data including:')</p>
                <ul class="list-unstyled ms-4">
                    <li class="mb-2">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        @lang('l.All user accounts and their data')
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        @lang('l.All system settings')
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        @lang('l.All uploaded files')
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        @lang('l.All database records')
                    </li>
                </ul>
            </div>

            <form id="resetForm" action="{{ route('dashboard.admins.settings-reset') }}" method="POST"
                  class="mt-4" enctype="multipart/form-data" onsubmit="return false;">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="mb-4">
                            <label class="form-label text-danger fw-bold fs-5 mb-3">
                                <i class="fas fa-keyboard me-2"></i>
                                @lang('l.Type RESET to confirm')
                            </label>
                            <input type="text"
                                   class="form-control form-control-lg text-center"
                                   id="confirmText"
                                   required
                                   autocomplete="off"
                                   placeholder="RESET">
                        </div>
                    </div>

                    <div class="col-12 text-center mt-4">
                        <button type="button"
                                id="resetButton"
                                class="btn btn-danger btn-lg px-5 py-3 shadow-sm">
                            <i class="fas fa-bomb me-2"></i>
                            @lang('l.Initialize System Reset')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.15) !important;
    }
    #resetButton {
        transition: all 0.3s ease;
    }
    #resetButton:hover {
        transform: scale(1.05);
    }
    .list-unstyled li {
        transition: all 0.3s ease;
    }
    .list-unstyled li:hover {
        transform: translateX(10px);
    }
</style>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // منع إرسال النموذج عند الضغط على Enter
    document.getElementById('resetForm').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });

    // تحسين تجربة المستخدم في حقل الإدخال
    const confirmInput = document.getElementById('confirmText');
    confirmInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    document.getElementById('resetButton').addEventListener('click', function(e) {
        e.preventDefault();

        if (document.getElementById('confirmText').value !== 'RESET') {
            Swal.fire({
                icon: 'error',
                title: '@lang("l.Invalid Confirmation")',
                text: '@lang("l.Please type RESET to confirm")',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        Swal.fire({
            title: '@lang("l.Warning!")',
            html: '<div class="text-danger fw-bold">@lang("l.Are you absolutely sure you want to reset the system?")</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '@lang("l.Yes, proceed")',
            cancelButtonText: '@lang("l.Cancel")',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            customClass: {
                popup: 'swal2-warning-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '@lang("l.Final Warning!")',
                    html: '<div class="text-danger fw-bold">@lang("l.This action cannot be undone. All data will be permanently deleted!")</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang("l.Yes, reset everything")',
                    cancelButtonText: '@lang("l.Cancel")',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result2) => {
                    if (result2.isConfirmed) {
                        Swal.fire({
                            title: '@lang("l.Last Chance!")',
                            html: '<div class="text-danger fw-bold">@lang("l.Are you really, really sure about this?")</div>',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '@lang("l.Yes, I understand the consequences")',
                            cancelButtonText: '@lang("l.No, take me back")',
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d'
                        }).then((result3) => {
                            if (result3.isConfirmed) {
                                Swal.fire({
                                    title: '@lang("l.Resetting System")',
                                    text: '@lang("l.This may take several minutes")',
                                    icon: 'info',
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        document.getElementById('resetForm').onsubmit = null;
                                        document.getElementById('resetForm').submit();
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });
});
</script>

<style>
.swal2-popup {
    font-size: 1.1rem !important;
}
.swal2-warning-popup {
    border-radius: 15px;
}
</style>


