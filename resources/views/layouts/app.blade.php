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
                <!-- Các dòng của bảng -->
                <!-- ... -->
            </table>
            {{ $tasks->links() }}
        </div>
    </div>

    <script>
        // Đóng thông báo tự động sau 3 giây
        setTimeout(function() {
            $(".alert").alert('close');
        }, 3000);
    </script>

@endsection
