<div class="comment mb-3" id="comment-{{ $comment->id }}" style="margin-right: {{ $comment->parent_id ? '40px' : '0' }}">
    <div class="comment-container p-3 border rounded shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <div class="avatar avatar-sm">
                @if($comment->user && $comment->user->photo)
                    <img src="{{ asset($comment->user->photo) }}" alt="Avatar" class="rounded-circle">
                @else
                    <span class="avatar-initial rounded-circle bg-label-info">
                        {{ $comment->user ? strtoupper(substr($comment->user->firstname, 0, 1)) : '?' }}
                    </span>
                @endif
            </div>
            <div class="w-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-0">
                            {{ $comment->user ? $comment->user->firstname . ' ' . $comment->user->lastname : __('l.Deleted User') }}
                        </h6>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if(!$comment->parent_id && $comment->all_replies_count > 0)
                            <button class="btn btn-sm btn-outline-primary toggle-replies" data-comment-id="{{ $comment->id }}">
                                <i class="fas fa-chevron-down"></i>
                                <span class="replies-count">({{ $comment->all_replies_count }}) {{ __('l.Replies') }}</span>
                            </button>
                        @endif
                        @can('edit blog')
                            <button type="button" class="btn btn-sm btn-danger delete-comment" data-id="{{ encrypt($comment->id) }}">
                                <i class="fa fa-trash ti-xs"></i>
                            </button>
                        @endcan
                    </div>
                </div>
                <p class="mb-2 mt-2">{{ $comment->content }}</p>
            </div>
        </div>
    </div>

    @if(!$comment->parent_id)
        <div class="replies-container collapse" id="replies-{{ $comment->id }}">
            @foreach($comment->children as $reply)
                @include('themes.default.back.admins.pages.blogs._comment', ['comment' => $reply])
            @endforeach
        </div>
    @else
        @if($comment->children->count() > 0)
            @foreach($comment->children as $reply)
                @include('themes.default.back.admins.pages.blogs._comment', ['comment' => $reply])
            @endforeach
        @endif
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-replies').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const repliesContainer = document.querySelector(`#replies-${commentId}`);
            const icon = this.querySelector('i');
            
            $(repliesContainer).collapse('toggle');
            
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    });
});
</script>

<style>
.comment-container {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

.comment-container:hover {
    box-shadow: 0 0.25rem 1rem rgba(0,0,0,.15) !important;
}

.toggle-replies {
    transition: all 0.3s ease;
    padding: 0.25rem 0.75rem;
}

.toggle-replies i {
    transition: transform 0.3s ease;
    margin-right: 5px;
}

.replies-container {
    transition: all 0.3s ease;
    padding-right: 20px;
    border-right: 2px solid #e9ecef;
    margin-right: 20px;
}

.replies-container .comment {
    margin-right: 20px;
}

.replies-container .replies-container {
    margin-right: 20px;
    border-right-style: dashed;
}

.replies-count {
    font-size: 0.875rem;
}
</style>
