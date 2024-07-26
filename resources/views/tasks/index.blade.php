@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <main id="main" class="main">
            <div class="row mb-3">
                <div class="col-md-6">
                    <button class="btn btn-primary" id="add-task-btn">Add Task</button>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <form id="filter-form" class="form-inline">
                        <div class="input-group">
                            <select id="project-filter" class="form-control ml-2">
                                <option value="">All Projects</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            <input id="search-input" type="text" class="form-control" placeholder="Search tasks">
                        </div>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>
                                <a href="#" class="sort-link" data-column="id" data-order="desc">
                                    ID
                                    <i class="bi bi-sort-alpha-up sort-icon desc" style="display: none;"></i>
                                    <i class="bi bi-sort-alpha-down sort-icon asc" style="display: none;"></i>
                                </a>
                            </th>
                            <th style="width: 15%">
                                <a href="#" class="sort-link" data-column="name" data-order="desc">
                                    Name
                                    <i class="bi bi-sort-alpha-up sort-icon" style="display: none;"></i>
                                    <i class="bi bi-sort-alpha-down sort-icon" style="display: none;"></i>
                                </a>
                            </th>
                            <th style="width: 60%">
                                <a href="#" class="sort-link" data-column="content" data-order="desc">
                                    Content
                                    <i class="bi bi-sort-alpha-up sort-icon" style="display: none;"></i>
                                    <i class="bi bi-sort-alpha-down sort-icon" style="display: none;"></i>
                                </a>
                            </th>
                            <th>
                                <a href="#" class="sort-link" data-column="project" data-order="desc">
                                    Project
                                    <i class="bi bi-sort-alpha-up sort-icon" style="display: none;"></i>
                                    <i class="bi bi-sort-alpha-down sort-icon" style="display: none;"></i>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>



                    <tbody id="tasks-table-body">
                        @foreach ($tasks as $task)
                            <tr class="align-middle" data-task-id="{{ $task->id }}">
                                <td class="text-center">{{ $task->id }}</td>
                                <td>{{ $task->name }}</td>
                                <td style="height: 80px;width: 50%">{{ $task->content }}</td>
                                <td>{{ $task->project ? $task->project->name : 'No Project' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-danger delete-btn"
                                        data-task-id="{{ $task->id }}">Delete</button>
                                    <button class="btn btn-warning edit-btn" data-task-id="{{ $task->id }}"
                                        style="display:inline-block;margin-left: 3px">Edit</button>
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

    <!-- Modal Confirm Delete -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Create Task -->
    @include('tasks.create-modal')

    <!-- Modal Edit Task -->
    @include('tasks.edit-modal')


    <style>
        .sort-icon {
            font-size: 1em;
            margin-left: 5px;
            display: inline-block;
            transition: opacity 0.3s;
        }


    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const projectFilter = document.getElementById('project-filter');
            const tasksTableBody = document.getElementById('tasks-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const sortLinks = document.querySelectorAll('.sort-link');
            const csrfToken = '{{ csrf_token() }}';

            let currentPage = 1;
            let currentQuery = '';
            let currentProjectId = '';
            let sortColumn = 'id';
            let sortOrder = 'desc';
            let taskToDelete = null;

            // Hiển thị modal tạo mới nhiệm vụ
            document.getElementById('add-task-btn').addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
                modal.show();
            });

            // Hiển thị modal chỉnh sửa nhiệm vụ
            document.addEventListener('click', function(event) {
                if (event.target && event.target.classList.contains('edit-btn')) {
                    const taskId = event.target.dataset.taskId;
                    fetch(`/tasks/${taskId}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            const task = data.task;
                            document.getElementById('edit-task-name').value = task.name;
                            document.getElementById('edit-task-content').value = task.content;
                            document.getElementById('edit-task-project').value = task.project_id || '';
                            document.getElementById('edit-task-id').value = task.id;
                            const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                            modal.show();
                        })
                        .catch(error => {
                            console.error('Error fetching task:', error);
                        });
                }

                if (event.target && event.target.classList.contains('delete-btn')) {
                    const taskId = event.target.dataset.taskId;
                    taskToDelete = taskId;
                    const taskName = event.target.closest('tr').querySelector('td:nth-child(2)')
                        .textContent;
                    const modalTitle = document.querySelector('#deleteConfirmModalLabel');
                    const modalBody = document.querySelector('.modal-body');
                    modalTitle.textContent = `Delete Task #${taskId}`;
                    modalBody.textContent = `Are you sure you want to delete the task "${taskName}"?`;
                    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                    modal.show();
                }
            });

            // Xác nhận xóa nhiệm vụ
            document.getElementById('confirm-delete-btn').addEventListener('click', function() {
                if (taskToDelete) {
                    fetch(`/tasks/${taskToDelete}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(() => {
                            document.querySelector(`tr[data-task-id="${taskToDelete}"]`).remove();
                            updateTable(currentQuery, currentProjectId, currentPage, sortColumn,
                                sortOrder);
                        })
                        .catch(error => {
                            console.error('Error deleting task:', error);
                        })
                        .finally(() => {
                            taskToDelete = null;
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'deleteConfirmModal'));
                            if (modal) {
                                modal.hide();
                            }
                        });
                }
            });

            // Tạo nhiệm vụ mới
            document.getElementById('create-task-form').addEventListener('submit', function(event) {
                event.preventDefault();
                const name = document.getElementById('task-name').value.trim();
                const content = document.getElementById('task-content').value.trim();
                const projectId = document.getElementById('task-project').value;
                fetch('/tasks', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            content: content,
                            project_id: projectId
                        })
                    })
                    .then(response => response.json())
                    .then(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'createTaskModal'));
                        modal.hide();
                        updateTable(currentQuery, currentProjectId, currentPage, sortColumn, sortOrder);
                    })
                    .catch(error => {
                        console.error('Error creating task:', error);
                    });
            });

            // Chỉnh sửa nhiệm vụ
            document.getElementById('edit-task-form').addEventListener('submit', function(event) {
                event.preventDefault();
                const name = document.getElementById('edit-task-name').value.trim();
                const content = document.getElementById('edit-task-content').value.trim();
                const projectId = document.getElementById('edit-task-project').value;
                const taskId = document.getElementById('edit-task-id').value;
                fetch(`/tasks/${taskId}`, {
                        method: 'PUT',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            content: content,
                            project_id: projectId
                        })
                    })
                    .then(response => response.json())
                    .then(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'editTaskModal'));
                        modal.hide();
                        updateTable(currentQuery, currentProjectId, currentPage, sortColumn, sortOrder);
                    })
                    .catch(error => {
                        console.error('Error updating task:', error);
                    });
            });

            function updateTable(query, projectId, page = 1, column = 'id', order = 'desc') {
                query = query.trim();
                currentQuery = query;
                currentPage = page;
                currentProjectId = projectId;
                sortColumn = column;
                sortOrder = order;

                const url = new URL('/api/tasks/search', window.location.origin);
                url.searchParams.append('query', query);
                url.searchParams.append('project_id', projectId);
                url.searchParams.append('page', page);
                url.searchParams.append('sort_column', column);
                url.searchParams.append('sort_order', order);

                fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        tasksTableBody.innerHTML = '';
                        if (data.data && Array.isArray(data.data)) {
                            data.data.forEach(task => {
                                const row = document.createElement('tr');
                                row.dataset.taskId = task.id;
                                row.innerHTML = `
                            <td>${task.id}</td>
                            <td>${task.name}</td>
                            <td>${task.content}</td>
                            <td>${task.project ? task.project.name : ''}</td>
                            <td>
                                <button class="btn btn-warning edit-btn" data-task-id="${task.id}">Edit</button>
                                <button class="btn btn-danger delete-btn" data-task-id="${task.id}">Delete</button>
                            </td>
                        `;
                                tasksTableBody.appendChild(row);
                            });
                        }

                        // Cập nhật phân trang
                        if (data.links) {
                    paginationLinks.innerHTML = data.links;

                    paginationLinks.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            const url = new URL(link.href);
                            const page = url.searchParams.get('page');
                            updateTable(currentQuery, projectName, page, sortColumn, sortOrder);
                        });
                    });
                } else {
                    paginationLinks.innerHTML = '';
                }

                        // Cập nhật các liên kết sắp xếp
                        sortLinks.forEach(link => {
                            const column = link.dataset.column;
                            const order = link.dataset.order;
                            link.classList.remove('sort-asc', 'sort-desc');
                            if (column === sortColumn) {
                                link.classList.add(order === 'asc' ? 'sort-asc' : 'sort-desc');
                                link.dataset.order = order === 'asc' ? 'desc' : 'asc';
                                link.querySelector('.sort-icon').style.display = 'inline';
                            } else { 
                                link.dataset.order = 'desc';
                                link.querySelector('.sort-icon').style.display = 'none';
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error updating table:', error);
                    });
            }

            // Xử lý sự kiện sắp xếp
            sortLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const column = link.dataset.column;
                    const order = link.dataset.order;
                    updateTable(currentQuery, currentProjectId, currentPage, column, order);
                });
            });

            // Xử lý tìm kiếm
            searchInput.addEventListener('input', function() {
                updateTable(searchInput.value, currentProjectId, 1, sortColumn, sortOrder);
            });

            // Xử lý bộ lọc dự án
            projectFilter.addEventListener('change', function() {
                updateTable(searchInput.value, projectFilter.value, 1, sortColumn, sortOrder);
            });

            // Khởi tạo bảng với dữ liệu mặc định
            updateTable(currentQuery, currentProjectId, currentPage, sortColumn, sortOrder);
        });
    </script>
@endsection
