@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <table class="table table striped">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
                @foreach($tasks as $task)
                    <tr>
                        <th>{{ $task->id }}</th>
                        <th>{{ $task->name }}</th>
                        <th>{{ $task->content }}</th>
                        <th>
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger ">Delete</button>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning">Edit</a>
                            </form>
                        </th>
                    </tr>
                @endforeach
            </table>
            {{ $tasks->links() }}
        </div>

    </div>

@endsection
