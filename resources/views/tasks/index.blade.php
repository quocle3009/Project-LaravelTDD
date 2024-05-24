@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
            </div>
            <!-- Search form -->
            <div class="col-md-6 card-header d-flex justify-content-end align-items-center">

                <!-- Search form -->
                <form method="GET" action="{{ route('tasks.index') }}" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search tasks"
                            value="{{ request()->query('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i> <!-- Font Awesome search icon -->
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <table class="table table-striped">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr class="align-middle">
                            <td class="text-center">{{ $task->id }}</td>
                            <td>{{ $task->name }}</td>
                            <td style="height: 80px;width: 70%">{{ $task->content }}</td>
                            <td class="text-center">
                                <form id="delete-form-{{ $task->id }}" method="POST"
                                    action="{{ route('tasks.destroy', $task->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger"
                                        onclick="confirmDelete('{{ $task->id }}')">Delete</button>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning"
                                        style="margin-left: 3px">Edit</a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-3"> <!-- Thay đổi kích thước phù hợp -->
                {{ $tasks->links() }}
            </div>
        </div>
        
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this task?')) {
                // Nếu người dùng chấp nhận, submit form xóa
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
    <script>
        window.onload = function() {
            var anchor = window.location.hash;
            if (anchor) {
                var element = document.querySelector(anchor);
                if (element) {
                    element.scrollIntoView();
                }
            }
        }
    </script>
    
@endsection
