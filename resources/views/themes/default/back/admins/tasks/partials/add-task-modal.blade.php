<!-- Modal Task -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">{{ __('l.Add New Task') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTaskForm" action="{{ route('dashboard.admins.tasks-store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="title" class="form-label required">{{ __('l.Task Title') }}</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label required">{{ __('l.Task Description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="assigned_to" class="form-label required">{{ __('l.Assign To') }}</label>
                            <select class="form-select select2" id="assigned_to" name="assigned_to" required>
                                <option value="">{{ __('l.Select User') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->firstname . ' ' . $user->lastname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="due_date" class="form-label required">{{ __('l.Due Date') }}</label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('l.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('l.Save Task') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
