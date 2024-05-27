@extends('layouts.app')
    @section('style')
        <style>
            /* Custom CSS for the create task form */
            .card {
                border-radius: 15px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                background-color: #f8f9fa;
                /* Màu nền cho header */
                border-bottom: 1px solid #dee2e6;
                /* Viền dưới cho header */
            }

            .form-group {
                margin-bottom: 20px;
                /* Khoảng cách giữa các input */
            }

            label {
                font-weight: bold;
                /* In đậm tiêu đề của input */
            }

            .btn-primary {
                background-color: #007bff;
                /* Màu nền cho nút */
                border-color: #007bff;
                /* Màu viền cho nút */
            }

            .btn-primary:hover {
                background-color: #0056b3;
                /* Màu nền hover */
                border-color: #0056b3;
                /* Màu viền hover */
            }
        </style>
    @endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2>Create task</h2>
                <form action="{{ route('tasks.store') }}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter task name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="content">Content:</label>
                                <input type="text" class="form-control" id="content" name="content"
                                    placeholder="Enter task content">
                                @error('content')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
