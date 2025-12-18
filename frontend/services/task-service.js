const TaskService = {
    /**
     * Fetches all tasks for the currently logged-in user from the backend.
     * @param {function} callback - Function to handle the returned tasks.
     */
    getTasks: function(callback) {
        const token = localStorage.getItem("user_token");
        if (!token) {
            callback([]);
            return;
        }

        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/tasks",
            method: "GET",
            headers: { "Authorization": "Bearer " + token },
            success: function(res) {
                callback(res.tasks || []);
            },
            error: function(err) {
                console.error("Failed to load tasks:", err);
                toastr.error("Failed to load tasks.");
                callback([]);
            }
        });
    },

    /**
     * Adds a new task to the database (Admin only).
     * @param {object} taskData - Object containing the task title.
     * @param {function} callback - Function to execute on success.
     */
    addTask: function(taskData, callback) {
        const token = localStorage.getItem("user_token");
        if (!token) {
            toastr.error("You must be logged in to add tasks.");
            return;
        }

        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/tasks",
            method: "POST",
            contentType: "application/json",
            headers: { "Authorization": "Bearer " + token },
            data: JSON.stringify(taskData),
            success: function(res) {
                toastr.success("Task added successfully.");
                if (callback) callback(res);
            },
            error: function(err) {
                console.error("Failed to add task:", err);
                toastr.error(err.responseJSON?.error || "Failed to add task.");
            }
        });
    },

    /**
     * Updates an existing task's title by its ID (Admin only).
     * @param {number} taskId - ID of the task to update.
     * @param {object} taskData - New task data.
     * @param {function} callback - Function to execute on success.
     */
    updateTask: function(taskId, taskData, callback) {
        const token = localStorage.getItem("user_token");
        if (!token) {
            toastr.error("You must be logged in to update tasks.");
            return;
        }

        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/tasks/" + taskId,
            method: "PUT",
            contentType: "application/json",
            headers: { "Authorization": "Bearer " + token },
            data: JSON.stringify(taskData),
            success: function(res) {
                toastr.success("Task updated successfully.");
                if (callback) callback(res);
            },
            error: function(err) {
                console.error("Failed to update task:", err);
                toastr.error(err.responseJSON?.error || "Failed to update task.");
            }
        });
    },

    /**
     * Deletes a task from the database by its ID (Admin only).
     * @param {number} taskId - ID of the task to delete.
     * @param {function} callback - Function to execute on success.
     */
    deleteTask: function(taskId, callback) {
        const token = localStorage.getItem("user_token");
        if (!token) {
            toastr.error("You must be logged in to delete tasks.");
            return;
        }

        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/tasks/" + taskId,
            method: "DELETE",
            headers: { "Authorization": "Bearer " + token },
            success: function(res) {
                toastr.success("Task deleted successfully.");
                if (callback) callback(res);
            },
            error: function(err) {
                console.error("Failed to delete task:", err);
                toastr.error(err.responseJSON?.error || "Failed to delete task.");
            }
        });
    }
};

/**
 * Global Event Delegation for Task Actions.
 * These listeners are attached to the document so they work for items 
 * loaded dynamically into the DOM by the SPA.
 */
$(document).ready(function() {
    
    // Add Task Button Delegation
    $(document).on("click", "#addTaskBtn", function() {
        const title = $('#newTaskTitle').val().trim();
        if (!title) {
            toastr.error("Task title cannot be empty.");
            return;
        }
        TaskService.addTask({ title: title }, function() {
            $('#newTaskTitle').val('');
            // Reload tasks manually or refresh view logic here
            if (typeof loadTasks === 'function') loadTasks('admin'); 
        });
    });

    // Delete Task Button Delegation
    $(document).on("click", ".deleteBtn", function() {
        const $li = $(this).closest('li');
        const taskId = $li.data('id');
        if (confirm("Are you sure you want to delete this task?")) {
            TaskService.deleteTask(taskId, function() {
                $li.remove();
            });
        }
    });

    // Edit Task Button Delegation
    $(document).on("click", ".editBtn", function() {
        const $li = $(this).closest('li');
        const taskId = $li.data('id');
        const currentTitle = $li.find('.task-title').text();
        const newTitle = prompt("Edit task title:", currentTitle);
        
        if (newTitle && newTitle.trim() !== "" && newTitle !== currentTitle) {
            TaskService.updateTask(taskId, { title: newTitle.trim() }, function() {
                $li.find('.task-title').text(newTitle.trim());
            });
        }
    });
});