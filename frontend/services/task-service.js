var TaskService = {
    init: function() {
        // Event listener za formu za dodavanje taskova
        // Koristimo .off().on() da spriječimo dupliranje listenera kod SPApp navigacije
        $(document).off("submit", "#addTaskForm").on("submit", "#addTaskForm", function(e){
            e.preventDefault();
            TaskService.addTask();
        });
    },

    /**
     * OVO JE FUNKCIJA KOJA JE FALILA.
     * Dohvata taskove sa servera i prikazuje ih u <ul id="taskList">.
     */
    loadUserTasks: function() {
        const token = localStorage.getItem("user_token");
        
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/tasks",
            method: "GET",
            headers: { "Authorization": "Bearer " + token },
            success: function(response) {
                // Backend može vratiti niz direktno ili objekat {data: [...]} ili {tasks: [...]}
                // Ovdje pokrivamo sve slučajeve
                let tasks = [];
                if (Array.isArray(response)) {
                    tasks = response;
                } else if (response.data) {
                    tasks = response.data;
                } else if (response.tasks) {
                    tasks = response.tasks;
                }

                let html = "";
                if (tasks.length === 0) {
                    html = `<li class="list-group-item text-muted text-center">No tasks yet. Add one above!</li>`;
                } else {
                    tasks.forEach(t => {
                        // Provjera da li je task završen (ako imaš status kolonu)
                        const isDone = t.status === 'COMPLETED' || t.status === 'DONE';
                        const badgeClass = t.priority_id == 3 ? 'bg-danger' : (t.priority_id == 2 ? 'bg-warning text-dark' : 'bg-success');
                        const priorityLabel = t.priority_id == 3 ? 'High' : (t.priority_id == 2 ? 'Medium' : 'Low');

                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="${isDone ? 'text-decoration-line-through text-muted' : ''}">
                                    ${t.title}
                                </span>
                                <span class="badge ${badgeClass} ms-2" style="font-size: 0.7em;">${priorityLabel}</span>
                            </div>
                            
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-danger" onclick="TaskService.deleteTask(${t.task_id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </li>`;
                    });
                }
                
                // Ubacivanje HTML-a u listu na account.html
                $("#taskList").html(html);
            },
            error: function(xhr) {
                console.error("Error loading tasks", xhr);
                $("#taskList").html(`<li class="list-group-item text-danger text-center">Error loading tasks from server.</li>`);
            }
        });
    },

    /**
     * Dodaje novi task
     */
    addTask: function() {
        const token = localStorage.getItem("user_token");
        
        // Prikupljanje podataka iz forme
        const taskData = {
            title: $("#taskTitle").val(),
            priority_id: $("#taskPriority").val(), // Ako koristiš prioritete
            description: "" // Opcionalno
        };

        if(!taskData.title) {
            toastr.warning("Please enter a task title");
            return;
        }

        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/tasks",
            method: "POST",
            data: JSON.stringify(taskData),
            contentType: "application/json",
            headers: { "Authorization": "Bearer " + token },
            success: function(response) {
                toastr.success("Task added successfully!");
                $("#taskTitle").val(""); // Očisti input polje
                TaskService.loadUserTasks(); // Osvježi listu da se vidi novi task
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON ? xhr.responseJSON.error : "Failed to add task");
            }
        });
    },
loadCategories: function() {
    // 1. UZMI TOKEN (ovo je vjerovatno falilo)
    const token = localStorage.getItem("user_token");

    $.ajax({
        url: Constants.PROJECT_BASE_URL + "/categories",
        method: "GET",
        // 2. POŠALJI TOKEN U HEADERU
        headers: { "Authorization": "Bearer " + token }, 
        
        success: function(response) {
            console.log("Stigle kategorije:", response); // <--- OVO POGLEDAJ U KONZOLI

            let html = '<option value="" selected disabled>Choose a category</option>';
            
            // 3. ODREDI GDJE JE NIZ PODATAKA
            // Backend nekad vrati samo niz, nekad {data: [...]}, nekad {categories: [...]}
            let categories = [];
            
            if (Array.isArray(response)) {
                categories = response;
            } else if (response.data) {
                categories = response.data;
            } else if (response.categories) {
                categories = response.categories;
            }

            // 4. GENERIŠI HTML
            categories.forEach(cat => {
                // Pazi da li je 'id' i 'name', provjeri u konzoli šta piše u objektu
                html += `<option value="${cat.id}">${cat.name}</option>`;
            });

            // Ubaci u HTML
            $("#categorySelect").html(html);
        },
        error: function(xhr) {
            console.error("Greška pri učitavanju kategorija:", xhr);
            $("#categorySelect").html('<option value="">Error loading categories</option>');
        }
    });
},
    /**
     * Briše task
     */
    deleteTask: function(id) {
        if(confirm("Are you sure you want to delete this task?")) {
            const token = localStorage.getItem("user_token");
            $.ajax({
                url: Constants.PROJECT_BASE_URL + "/tasks/" + id,
                method: "DELETE",
                headers: { "Authorization": "Bearer " + token },
                success: function() {
                    toastr.success("Task deleted.");
                    TaskService.loadUserTasks(); // Osvježi listu
                },
                error: function(xhr) {
                    toastr.error("Failed to delete task.");
                }
            });
        }
    }
};


// Inicijalizacija servisa kada je dokument spreman
$(document).ready(function() {
    TaskService.init();
});