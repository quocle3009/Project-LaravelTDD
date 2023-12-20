@extends('layouts.app')

@section('content')

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
        <div class="row justify-content-center">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->name }}</td>
                        <td>{{ $task->content }}</td>
                        <td>
                            <form id="delete-form-{{ $task->id }}" method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ $task->id }}')">Delete</button>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning">Edit</a>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $tasks->links() }}
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this task?')) {
                // Nếu người dùng chấp nhận, submit form xóa
                document.getElementById('delete-form-' + id).submit();
            }
        }

        // Đóng thông báo tự động sau 3 giây
        setTimeout(function() {
            $(".alert").alert('close');
        }, 3000);
    </script>

@endsection
