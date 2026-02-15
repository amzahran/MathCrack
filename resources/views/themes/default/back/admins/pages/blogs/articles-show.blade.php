@extends('themes.default.layouts.back.master')

@section('title')
    {{ Str::title($blog->title) }}
@endsection

@section('content')
    @can('show blog')
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ Str::title($blog->title) }}</h3>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-body">
                    {!! $blog->content !!}
                </div>
            </div>

            <!-- قسم التعليقات -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">@lang('l.Comments')</h4>
                </div>
                <div class="card-body">
                    @foreach($blog->comments as $comment)
                        @include('themes.default.back.admins.pages.blogs._comment', ['comment' => $comment])
                    @endforeach
                </div>
            </div>
        </div>
    @endcan
    <!-- / Content -->
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // حذف التعليق
        $(document).on('click', '.delete-comment', function(e) {
            e.preventDefault();
            let button = $(this);
            let commentId = button.data('id');

            Swal.fire({
                title: '@lang("l.Are you sure?")',
                text: '@lang("l.You wont be able to revert this!")',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang("l.Yes, delete it!")',
                cancelButtonText: '@lang("l.Cancel")',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '{{ route("dashboard.admins.blogs.comments-delete") }}?id=' + commentId;
                }
            });
        });
    });
</script>
@endsection
