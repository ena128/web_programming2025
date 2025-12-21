var UserAdminService = {
    /**
     * Učitava sve korisnike iz baze i prikazuje ih u tabeli Admin Panela.
     */
    loadAllUsers: function() {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "/users",
            method: "GET",
            headers: { 
                "Authorization": "Bearer " + localStorage.getItem("user_token") 
            },
            success: function(users) {
                let html = "";
                if (users.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
                } else {
                    users.forEach(user => {
                        html += `
                        <tr>
                            <td>${user.id}</td>
                            <td><strong>${user.name || 'N/A'}</strong></td>
                            <td>${user.email}</td>
                            <td><span class="badge bg-info text-dark">${user.role}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="UserAdminService.openEditModal(${user.id})">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="UserAdminService.deleteUser(${user.id})">Delete</button>
                                </div>
                            </td>
                        </tr>`;
                    });
                }
                $("#userTableBody").html(html);
            },
            error: function(xhr) {
                toastr.error("Failed to load users. Are you an admin?");
                console.error("Admin Error:", xhr.responseText);
            }
        });
    },

    /**
     * Briše korisnika na osnovu ID-a.
     */
    deleteUser: function(id) {
        if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
            $.ajax({
                url: Constants.PROJECT_BASE_URL + "/users/" + id,
                method: "DELETE",
                headers: { 
                    "Authorization": "Bearer " + localStorage.getItem("user_token") 
                },
                success: function() {
                    toastr.success("User deleted successfully.");
                    UserAdminService.loadAllUsers(); // Osvježi tabelu
                },
                error: function(xhr) {
                    toastr.error("Could not delete user.");
                }
            });
        }
    },

    /**
     * Funkcija za otvaranje modala za dodavanje korisnika.
     */
    openAddUserModal: function() {
        // Ovdje možeš pokrenuti Bootstrap modal ili preusmjeriti na formu
        toastr.info("Add User feature - Modal implementation pending.");
    },

    /**
     * Funkcija za uređivanje postojećeg korisnika.
     */
    openEditModal: function(id) {
        toastr.info("Edit User ID: " + id + " - Modal implementation pending.");
    }
};