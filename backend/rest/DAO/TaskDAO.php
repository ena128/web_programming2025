<?php
require_once __DIR__ . '/BaseDAO.php';

class TaskDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("tasks", "task_id");
    }

    public function createTask($user_id, $title, $due_date, $status, $priority_id, $category_id) {
        $query = "INSERT INTO tasks (user_id, title, due_date, status, priority_id, category_id) 
                  VALUES (:user_id, :title, :due_date, :status, :priority_id, :category_id)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'due_date' => $due_date,
            'status' => $status,
            'priority_id' => $priority_id,
            'category_id' => $category_id
        ]);
        return $this->connection->lastInsertId();
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTask($task_id, $title, $due_date, $status, $priority_id, $category_id) {
        $query = "UPDATE tasks SET title = :title, due_date = :due_date, status = :status, 
                  priority_id = :priority_id, category_id = :category_id WHERE task_id = :task_id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'task_id' => $task_id,
            'title' => $title,
            'due_date' => $due_date,
            'status' => $status,
            'priority_id' => $priority_id,
            'category_id' => $category_id
        ]);
        return $stmt->rowCount(); 
    }

    public function deleteTask($task_id) {
        $stmt = $this->connection->prepare("DELETE FROM tasks WHERE task_id = :task_id");
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        return $stmt->rowCount(); 
    }
}
?>
