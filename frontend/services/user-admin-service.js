var UserAdminService = {
    /**
     * Dohvata sve korisnike i prikazuje ih u tabeli
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
                            <td>
                                <span class="badge ${user.role === 'ADMIN' ? 'bg-danger' : 'bg-info text-dark'}">
                                    ${user.role}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="UserAdminService.deleteUser(${user.id})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>`;
                    });
                }
                $("#userTableBody").html(html);
            },
            error: function(xhr) {
                console.error("Failed to load users:", xhr);
                toastr.error("Could not load user list. Are you authorized?");
            }
        });
    },

    /**
     * Briše korisnika
     */
    deleteUser: function(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: Constants.PROJECT_BASE_URL + "/users/" + id,
                method: "DELETE",
                headers: { 
                    "Authorization": "Bearer " + localStorage.getItem("user_token") 
                },
                success: function(result) {
                    toastr.success("User deleted successfully.");
                    UserAdminService.loadAllUsers(); // Osvježi tabelu
                },
                error: function(xhr) {
                    toastr.error("Failed to delete user.");
                }
            });
        }
    }
};