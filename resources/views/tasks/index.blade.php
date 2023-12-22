@extends('layouts.app')

@section('content')

    <div class="container">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
        <div class="row justify-content-center">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th style="width: 1000px">Content</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->name }}</td>
                        <td style="height: 80px;width: 70%">{{ $task->content }}</td>
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
    </script>

@endsection
