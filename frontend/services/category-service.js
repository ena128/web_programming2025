let CategoryService = {
    init: function () {
        // Validacija forme za dodavanje kategorije
        $("#addCategoryForm").validate({
            submitHandler: function (form) {
                var category = Object.fromEntries(new FormData(form).entries());
                CategoryService.addCategory(category);
                form.reset();
            },
        });

        // Validacija forme za editovanje kategorije
        $("#editCategoryForm").validate({
            submitHandler: function (form) {
                var category = Object.fromEntries(new FormData(form).entries());
                CategoryService.editCategory(category);
            },
        });

        CategoryService.getAllCategories();
    },

    openAddModal: function () {
        $("#addCategoryModal").show();
    },

    addCategory: function (category) {
        $.blockUI({ message: '<h3>Processing...</h3>' });
        RestClient.post('categories', category, function (response) {
            toastr.success("Category added successfully");
            $.unblockUI();
            CategoryService.getAllCategories();
            CategoryService.closeModal();
        }, function (response) {
            toastr.error(response.message);
            $.unblockUI();
            CategoryService.closeModal();
        });
    },

    getAllCategories: function () {
        RestClient.get("categories", function (data) {
            Utils.datatable('categories-table', [
                { data: 'id', title: 'ID' },
                { data: 'name', title: 'Name' },
                {
                    title: 'Actions',
                    render: function (data, type, row, meta) {
                        const rowStr = encodeURIComponent(JSON.stringify(row));
                        return `<div class="d-flex justify-content-center gap-2 mt-3">
                            <button class="btn btn-primary" onclick="CategoryService.openEditModal('${row.id}')">Edit</button>
                            <button class="btn btn-danger" onclick="CategoryService.openConfirmationDialog(decodeURIComponent('${rowStr}'))">Delete</button>
                        </div>`;
                    }
                }
            ], data, 10);
        }, function (xhr, status, error) {
            toastr.error("Failed to load categories");
        });
    },

    getCategoryById: function (id) {
        RestClient.get('categories/' + id, function (data) {
            localStorage.setItem('selected_category', JSON.stringify(data));
            $("#edit_category_id").val(data.id);
            $('input[name="name"]').val(data.name);
            $.unblockUI();
        }, function (xhr, status, error) {
            toastr.error("Failed to fetch category data");
            $.unblockUI();
        });
    },

    openEditModal: function (id) {
        $.blockUI({ message: '<h3>Loading...</h3>' });
        $("#editCategoryModal").show();
        CategoryService.getCategoryById(id);
    },

    editCategory: function (category) {
        $.blockUI({ message: '<h3>Processing...</h3>' });
        RestClient.put('categories/' + category.id, category, function (response) {
            toastr.success("Category updated successfully");
            $.unblockUI();
            CategoryService.getAllCategories();
            CategoryService.closeModal();
        }, function (response) {
            toastr.error(response.message);
            $.unblockUI();
        });
    },

    openConfirmationDialog: function (category) {
        category = JSON.parse(category);
        $("#deleteCategoryModal").modal("show");
        $("#delete-category-body").html("Do you want to delete category: " + category.name + "?");
        $("#delete_category_id").val(category.id);
    },

    deleteCategory: function () {
        const id = $("#delete_category_id").val();
        RestClient.delete('categories/' + id, null, function (response) {
            toastr.success(response.message);
            CategoryService.getAllCategories();
            CategoryService.closeModal();
        }, function (response) {
            toastr.error(response.message);
            CategoryService.closeModal();
        });
    },

    closeModal: function () {
        $("#addCategoryModal").hide();
        $("#editCategoryModal").hide();
        $("#deleteCategoryModal").modal("hide");
    }
};

$(document).ready(function () {
    CategoryService.init();
});
