@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <main id="main" class="main">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
                </div>
                <!-- Search form -->
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <form id="search-form" class="form-inline">
                        <div class="input-group">
                            <input id="search-input" type="text" class="form-control" placeholder="Search tasks">
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
                    <tbody id="tasks-table-body">
                        @foreach ($tasks as $task)
                            <tr class="align-middle">
                                <td class="text-center">{{ $task->id }}</td>
                                <td>{{ $task->name }}</td>
                                <td style="height: 80px;width: 70%">{{ $task->content }}</td>
                                <td class="text-center">
                                    <form id="delete-form-{{ $task->id }}" method="POST"
                                        action="{{ route('tasks.destroy', $task->id) }}" style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning"
                                        style="display:inline-block;margin-left: 3px">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="d-flex justify-content-center">
                    <div id="pagination-links">
                        {{ $tasks->appends(['query' => request()->get('query')])->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const tasksTableBody = document.getElementById('tasks-table-body');
            const paginationLinks = document.getElementById('pagination-links');

            // Cập nhật URL API
            const apiUrl = '/api/tasks/search';
            const csrfToken = '{{ csrf_token() }}'; // Nếu cần CSRF Token cho các phương thức khác như POST

            let currentPage = 1; // Trang hiện tại
            let currentQuery = ''; // Từ khóa tìm kiếm hiện tại

            // Function to update table based on search query
            function updateTable(query, page = 1) {
                // Đặt query thành chuỗi rỗng nếu không có giá trị
                query = query.trim();
                if (query === undefined || query === null) {
                    query = '';
                }

                currentQuery = query;
                currentPage = page;

                // Xây dựng URL với tham số
                const url = new URL(apiUrl, window.location.origin);
                url.searchParams.append('query', query);
                url.searchParams.append('page', page);

                // Gửi yêu cầu fetch
                fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Hiển thị mã lỗi và phản hồi từ server
                            return response.text().then(text => {
                                throw new Error(
                                    `HTTP error! status: ${response.status}, details: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data received:', data); // Kiểm tra dữ liệu nhận được
                        tasksTableBody.innerHTML = ''; // Xóa dữ liệu cũ

                        if (data.data && Array.isArray(data.data)) {
                            data.data.forEach(task => {
                                const row = document.createElement('tr');
                                row.classList.add('align-middle');

                                row.innerHTML = `
                                <td class="text-center">${task.id}</td>
                                <td>${task.name}</td>
                                <td style="height: 80px;width: 70%">${task.content}</td>
                                <td class="text-center">
                                    <form id="delete-form-{{ $task->id }}" method="POST"
                                        action="{{ route('tasks.destroy', $task->id) }}" style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning"
                                        style="display:inline-block;margin-left: 3px">Edit</a>
                                </td>
                            `;

                                tasksTableBody.appendChild(row); // Thêm hàng vào bảng
                            });
                        } else {
                            console.error('Data is not an array or is undefined');
                        }

                        // Cập nhật liên kết phân trang
                        if (data.links) {
                            paginationLinks.innerHTML = data.links;

                            // Thêm sự kiện cho các liên kết phân trang
                            paginationLinks.querySelectorAll('a').forEach(link => {
                                link.addEventListener('click', function(event) {
                                    event.preventDefault();
                                    const url = new URL(link.href);
                                    const page = url.searchParams.get('page');
                                    updateTable(currentQuery, page);
                                });
                            });
                        } else {
                            // Nếu không có liên kết phân trang, xóa liên kết cũ
                            paginationLinks.innerHTML = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching tasks:', error);
                    });
            }

            // Event listener cho ô tìm kiếm
            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim();
                if (query !== currentQuery) {
                    updateTable(query);
                    I
                }
            });

            // Tải dữ liệu ban đầu
            const initialQuery = searchInput.value.trim();
            updateTable(initialQuery);
        });
    </script>
@endsection
