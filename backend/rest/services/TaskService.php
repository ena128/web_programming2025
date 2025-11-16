<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/TaskDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

class TaskService extends BaseService {

    // Constructor: initializes the DAO
    public function __construct() {
        parent::__construct(new TaskDAO());
    }

    

    // Validate the task data
    private function validateTaskData($data) {
        if (empty($data['title'])) {
            throw new Exception("Task title is required.");
        }

        // Validate due date format if provided
        if (isset($data['due_date']) && !strtotime($data['due_date'])) {
            throw new Exception("Due date is invalid.");
        }

        // Validate user_id
        if (!isset($data['user_id']) || !is_numeric($data['user_id'])) {
            throw new Exception("Valid user ID is required.");
        }

        return true;
    }
}
?>