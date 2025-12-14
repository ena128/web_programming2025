$(document).ready(function () {
    var app = $.spapp({
        defaultView: "home",
        templateDir: "views/"
    });

    // Generate navigation menu
    UserService.generateMenuItems();

    // Fetch current user and adjust UI based on role
    UserService.getCurrentUser(user => {
        if (!user) {
            // Not logged in → redirect
            window.location.replace('login.html');
            return;
        }

        $('#username').text(user.username);
        const userRole = user.role;

        // Show/hide admin panel
        if (userRole === 'admin') {
            $('#adminPanel').show();
        } else {
            $('#adminPanel').hide();
        }

        // Load tasks
        loadTasks(userRole);

        // Add new task (admin only)
        $('#addTaskBtn').off('click').on('click', function () {
            const title = $('#newTaskTitle').val().trim();
            if (!title) return toastr.error("Task title cannot be empty.");

            TaskService.addTask({ title: title }, () => {
                $('#newTaskTitle').val('');
                loadTasks(userRole);
            });
        });
    });

    app.run();
});

// Load and render tasks
function loadTasks(role) {
    TaskService.getTasks(tasks => {
        renderTasks(tasks, role);
    });
}

// Render tasks with role-based buttons
function renderTasks(tasks, role) {
    const $taskList = $('#taskList');
    $taskList.empty();

    if (!tasks || tasks.length === 0) {
        $taskList.append('<li class="list-group-item">No tasks found.</li>');
        return;
    }

    tasks.forEach(task => {
        let taskHtml = `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${task.id}">
                            <span class="task-title">${task.title}</span>`;

        if (role === 'admin') {
            taskHtml += `
                <div>
                    <button class="editBtn btn btn-sm btn-warning me-2">Edit</button>
                    <button class="deleteBtn btn btn-sm btn-danger">Delete</button>
                </div>
            `;
        }

        taskHtml += `</li>`;
        $taskList.append(taskHtml);
    });

    // Admin actions
    if (role === 'admin') {
        $('.deleteBtn').off('click').on('click', function () {
            const taskId = $(this).closest('li').data('id');
            TaskService.deleteTask(taskId, () => loadTasks(role));
        });

        $('.editBtn').off('click').on('click', function () {
            const $li = $(this).closest('li');
            const taskId = $li.data('id');
            const currentTitle = $li.find('.task-title').text();
            const newTitle = prompt("Edit task title:", currentTitle);
            if (!newTitle) return;

            TaskService.updateTask(taskId, { title: newTitle }, () => loadTasks(role));
        });
    }
}
