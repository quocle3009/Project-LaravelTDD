@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2>Edit task</h2>
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <div class="card">
                        <div class="card-header">
                            <input type="text" class="form-group" value="{{ $task->name }}" name="name"
                                   placeholder="Name ...">
                            @error('name')
                            <span id="name-error" class="error text-danger" style="...">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-group" value="{{ $task->content }}" name="content"
                                   placeholder="Content...">
                            @error('content')
                            <span id="name-error" class="error text-danger" style="...">{{ $message }}</span>
                            @enderror
                        </div>
                        <button class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
