@extends('layouts.app')

@section('content')
    {{-- <div class="container"> --}}
        <main id="main" class="main">
        {{-- <div class="row">
            <div class="col-md-6">
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a>
            </div>
            <!-- Search form -->
            <div class="col-md-6 card-header d-flex justify-content-end align-items-center">

            </div>
        </div> --}}
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
                                <form id="delete-form-{{ $task->id }}" method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ $task->id }}')">Delete</button>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning" style="margin-left: 3px">Edit</a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="d-flex justify-content-center">
                {{ $tasks->links() }}
            </div>
        </div>
    {{-- </div> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const tableBody = document.getElementById('tasks-table-body');
    
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                fetchTasks(`/search?query=${query}`);
            });
    
            function fetchTasks(url) {
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
    
            function displaySearchResults(results) {
                tableBody.innerHTML = ''; // Clear the table before adding new results
    
                results.data.forEach(task => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-center">${task.id}</td>
                        <td>${task.name}</td>
                        <td style="height: 80px;width: 70%">${task.content}</td>
                        <td class="text-center align-middle">
                            <div class="d-flex justify-content-center" style="gap: 5px;">
                                <form id="delete-form-${task.id}" method="POST" action="/tasks/${task.id}">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="btn btn-danger delete-button" data-task-id="${task.id}">Delete</button>
                                </form>
                                <a href="/tasks/${task.id}/edit" class="btn btn-warning edit-button" data-task-id="${task.id}">Edit</a>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
    
                addDeleteEventListeners();
                updatePagination(results.links);
            }
    
            function addDeleteEventListeners() {
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', function() {
                        const taskId = this.getAttribute('data-task-id');
                        if (confirm('Are you sure you want to delete this task?')) {
                            fetch(`/tasks/${taskId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ _method: 'DELETE' })
                            })
                            .then(response => {
                                if (response.ok) {
                                    searchInput.dispatchEvent(new Event('input'));
                                } else {
                                    console.error('Failed to delete the task.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                        }
                    });
                });
            }
    
            function updatePagination(links) {
                const pagination = document.querySelector('.pagination');
                if (pagination) {
                    pagination.innerHTML = links.map(link => {
                        const activeClass = link.active ? ' active' : '';
                        return `<li class="page-item${activeClass}"><a class="page-link" href="${link.url}">${link.label}</a></li>`;
                    }).join('');
                    attachPaginationEventListeners();
                }
            }
    
            // function attachPaginationEventListeners() {
            //     document.querySelectorAll('.pagination a').forEach(link => {
            //         link.addEventListener('click', function(event) {
            //             event.preventDefault(); // Prevent default page reload
            //             const url = this.getAttribute('href');
            //             fetchTasks(url);
            //         });
            //     });
            // }
    
            addDeleteEventListeners();
            attachPaginationEventListeners();
        });
    
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this task?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    
        window.onload = function() {
            const anchor = window.location.hash;
            if (anchor) {
                const element = document.querySelector(anchor);
                if (element) {
                    element.scrollIntoView();
                }
            }
        }
    </script>
    </main>
@endsection
