const CategoryService = {
    /**
     * Fetches all categories and populates the categories table.
     */
    getAllCategories: function () {
        RestClient.get("categories", function (data) {
            Utils.datatable('categories-table', [
                { data: 'id', title: 'ID' },
                { data: 'name', title: 'Name' },
                {
                    title: 'Actions',
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-primary edit-category-btn" data-id="${row.id}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-category-btn" data-id="${row.id}" data-name="${row.name}">Delete</button>
                            </div>`;
                    }
                }
            ], data);
        }, function (error) {
            toastr.error("Failed to load categories.");
        });
    },

    /**
     * Adds a new category.
     */
    addCategory: function (entity) {
        RestClient.post('categories', entity, function (response) {
            toastr.success("Category added successfully.");
            CategoryService.getAllCategories();
            $("#addCategoryModal").modal("hide");
        }, function (error) {
            toastr.error(error.responseJSON?.message || "Failed to add category.");
        });
    },
    populateCategoryDropdown: function(containerId) {
        const $select = $(containerId);
        $select.html('<option value="" selected disabled>Loading...</option>');

        RestClient.get("categories", function (data) {
            let html = '<option value="" selected disabled>Choose a category...</option>';
            
            // Provjera da li je data niz ili objekat
            // (RestClient obično vraća direktno podatke, ali za svaki slučaj)
            const categories = data.data ? data.data : data;

            if (categories && categories.length > 0) {
                categories.forEach(function(cat) {
                    html += `<option value="${cat.category_id}">${cat.name}</option>`;
                });
            } else {
                html = '<option value="" disabled>No categories found</option>';
            }

            $select.html(html);
        }, function (error) {
            $select.html('<option value="" disabled>Error loading categories</option>');
            console.error(error);
        });
    }
};


/**
 * Event Delegation for Category Actions
 */
$(document).ready(function () {
    // Add Category Form Submission
    $(document).on("submit", "#addCategoryForm", function (e) {
        e.preventDefault();
        const entity = Object.fromEntries(new FormData(this).entries());
        CategoryService.addCategory(entity);
        this.reset();
    });

    // Delete Category Button
    $(document).on("click", ".delete-category-btn", function () {
        const id = $(this).data("id");
        const name = $(this).data("name");
        if (confirm(`Are you sure you want to delete category: ${name}?`)) {
            RestClient.delete(`categories/${id}`, null, function () {
                toastr.success("Category deleted.");
                CategoryService.getAllCategories();
            });
        }
    });
});