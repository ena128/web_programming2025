<?php
require_once __DIR__ . '/BaseDAO.php';

class PriorityDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("priority", "priority_id");
    }

    // Create a new priority
    public function createPriority($name, $color) {
        $query = "INSERT INTO priority (name, color) VALUES (:name, :color)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'name' => $name,
            'color' => $color
        ]);
        return $this->connection->lastInsertId();
    }

    // Get all priorities
    public function getAllPriorities() {
        return $this->getAll("SELECT * FROM priority");
    }

    // Get priority by ID
    public function getPriorityById($priority_id) {
        return $this->getById($priority_id);
    }

    // Update priority by ID
    public function updatePriority($priority_id, $name, $color) {
        $query = "UPDATE priority SET name = :name, color = :color WHERE priority_id = :priority_id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'priority_id' => $priority_id,
            'name' => $name,
            'color' => $color
        ]);
        return $stmt->rowCount(); // Return how many rows were affected
    }

    // Delete priority by ID
    public function deletePriority($priority_id) {
        $stmt = $this->connection->prepare("DELETE FROM priority WHERE priority_id = :priority_id");
        $stmt->bindParam(':priority_id', $priority_id);
        $stmt->execute();
        return $stmt->rowCount(); // Return how many rows were affected
    }
}
?>