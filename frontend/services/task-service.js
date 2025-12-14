const TaskService = {
    // Get all tasks for the logged-in user
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

    // Add a new task (admin only)
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

    // Update task by ID (admin only)
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

    // Delete task by ID (admin only)
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
