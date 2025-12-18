const PriorityService = {
    /**
     * Fetches priorities for dropdown menus.
     */
    getPriorities: function (selector) {
        RestClient.get("priorities", function (data) {
            let options = '<option value="">Select Priority</option>';
            data.forEach(priority => {
                options += `<option value="${priority.id}">${priority.name}</option>`;
            });
            $(selector).html(options);
        });
    }
};
