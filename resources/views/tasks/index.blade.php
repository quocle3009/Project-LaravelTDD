@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <main id="main" class="main">
            <div class="row mb-3">
                <div class="col-md-6">
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
                </div>
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
                            <th><a href="#" class="sort-link" data-column="id" data-order="desc">ID</a></th>
                            <th><a href="#" class="sort-link" data-column="name" data-order="asc">Name</a></th>
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
            const sortLinks = document.querySelectorAll('.sort-link');

            const apiUrl = '/api/tasks/search';
            const csrfToken = '{{ csrf_token() }}';

            let currentPage = 1;
            let currentQuery = '';
            let sortColumn = 'id';
            let sortOrder = 'desc';

            function updateTable(query, page = 1, column = 'id', order = 'desc') {
                query = query.trim();
                currentQuery = query;
                currentPage = page;
                sortColumn = column;
                sortOrder = order;

                const url = new URL(apiUrl, window.location.origin);
                url.searchParams.append('query', query);
                url.searchParams.append('page', page);
                url.searchParams.append('sort_column', column);
                url.searchParams.append('sort_order', order);

                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status}, details: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    tasksTableBody.innerHTML = '';

                    if (data.data && Array.isArray(data.data)) {
                        data.data.forEach(task => {
                            const row = document.createElement('tr');
                            row.classList.add('align-middle');

                            row.innerHTML = `
                                <td class="text-center">${task.id}</td>
                                <td>${task.name}</td>
                                <td style="height: 80px;width: 70%">${task.content}</td>
                                <td class="text-center">
                                    <form id="delete-form-${task.id}" method="POST" action="/tasks/${task.id}" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    <a href="/tasks/${task.id}/edit" class="btn btn-warning" style="display:inline-block;margin-left: 3px">Edit</a>
                                </td>
                            `;

                            tasksTableBody.appendChild(row);
                        });
                    }

                    if (data.links) {
                        paginationLinks.innerHTML = data.links;

                        paginationLinks.querySelectorAll('a').forEach(link => {
                            link.addEventListener('click', function(event) {
                                event.preventDefault();
                                const url = new URL(link.href);
                                const page = url.searchParams.get('page');
                                updateTable(currentQuery, page, sortColumn, sortOrder);
                            });
                        });
                    } else {
                        paginationLinks.innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching tasks:', error);
                });
            }

            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim();
                updateTable(query, 1, sortColumn, sortOrder);
            });

            sortLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const column = link.dataset.column;
                    const order = link.dataset.order;

                    const newOrder = order === 'asc' ? 'desc' : 'asc';
                    link.dataset.order = newOrder;

                    updateTable(currentQuery, currentPage, column, newOrder);
                });
            });

            const initialQuery = searchInput.value.trim();
            updateTable(initialQuery, 1, sortColumn, sortOrder);
        });
    </script>
@endsection
