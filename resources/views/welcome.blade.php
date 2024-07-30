<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel To-Do List</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>To-Do List</h1>
    <input type="text" id="task-title" placeholder="Enter a task">
    <button id="add-task">Add Task</button>
    <button id="show-tasks">Show All Tasks</button>
    
    <table id="task-table" border="1">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
            // Add a new task
            $('#add-task').click(function () {
                let title = $('#task-title').val();
                $.ajax({
                    url: '/tasks',
                    type: 'POST',
                    data: { title: title },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (task) {
                        $('#task-table tbody').append(`<tr data-id="${task.id}"><td>${task.title}</td><td><input type="checkbox" class="complete-task"></td><td><button class="edit-task">Edit</button> <button class="delete-task">Delete</button></td></tr>`);
                        $('#task-title').val('');
                    },
                    error: function (response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            // Show all tasks
            $('#show-tasks').click(function () {
                $.get('/tasks', function (tasks) {
                    $('#task-table tbody').empty();
                    tasks.forEach(task => {
                        $('#task-table tbody').append(`<tr data-id="${task.id}"><td>${task.title}</td><td><input type="checkbox" class="complete-task" ${task.completed ? 'checked' : ''}></td><td><button class="edit-task">Edit</button> <button class="delete-task">Delete</button></td></tr>`);
                    });
                });
            });

            // Mark task as completed
            $('#task-table').on('change', '.complete-task', function () {
                let taskId = $(this).closest('tr').data('id');
                let isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/tasks/check/${taskId}`,
                    type: 'PUT',
                    data: { completed: isChecked },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        if (isChecked) {
                            $(this).closest('tr').fadeOut();
                        } else {
                            $(this).closest('tr').fadeIn();
                        }
                    }.bind(this)
                });
            });

            // Edit task
            $('#task-table').on('click', '.edit-task', function () {
                let row = $(this).closest('tr');
                let taskId = row.data('id');
                let currentTitle = row.find('td:first').text().trim();
                row.html(`
                    <td><input type="text" class="edit-input" value="${currentTitle}"></td>
                    <td></td>
                    <td><button class="save-task">Save</button> <button class="cancel-task">Cancel</button></td>
                `);
            });

            // Save task 
            $('#task-table').on('click', '.save-task', function () {
                let row = $(this).closest('tr');
                let taskId = row.data('id');
                let newTitle = row.find('.edit-input').val();

                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'PUT',
                    data: { title: newTitle },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (task) {
                        row.html(`<td>${task.title}</td><td><input type="checkbox" class="complete-task" ${task.completed ? 'checked' : ''}></td><td><button class="edit-task">Edit</button> <button class="delete-task">Delete</button></td>`);
                    },
                    error: function (response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            // Cancel editing
            $('#task-table').on('click', '.cancel-task', function () {
                let row = $(this).closest('tr');
                let taskId = row.data('id');
                let originalTitle = row.find('.edit-input').val();

                row.html(`<td>${originalTitle}</td><td><input type="checkbox" class="complete-task" ${row.find('.complete-task').is(':checked') ? 'checked' : ''}></td><td><button class="edit-task">Edit</button> <button class="delete-task">Delete</button></td>`);
            });

            // Delete task
            $('#task-table').on('click', '.delete-task', function () {
                if (confirm('Are you sure you want to delete this task?')) {
                    let taskId = $(this).closest('tr').data('id');
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            $(`tr[data-id="${taskId}"]`).remove();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>