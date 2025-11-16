<?php
// BaseService.php
abstract class BaseService {
    protected $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    // Validate data before creation (specific to entity if needed)
    protected function validateBeforeCreate($data) {
        if ($this->dao instanceof ActivityLogsDAO) {
            if (empty($data['user_id'])) {
                throw new Exception("Missing required field: 'user_id'.", 400);
            }

            $userDAO = new UserDAO();
            if (!$userDAO->getById($data['user_id'])) {
                throw new Exception("User with ID {$data['user_id']} does not exist.", 404);
            }

            if (empty($data['action'])) {
                throw new Exception("Missing required field: 'action'.", 400);
            }
        }

        // Add more entity-specific validations here if needed
    }

    // Generic create method
    public function create($data) {
        $this->validateBeforeCreate($data);

        $insertedId = $this->dao->create($data);
        if ($insertedId) {
            return $this->dao->getById($insertedId);
        } else {
            throw new Exception("Error creating entity.", 500);
        }
    }

    // Generic method to retrieve by ID
    public function getById($id) {
        $result = $this->dao->getById($id);
        if (!$result) {
            throw new Exception("Entity with ID $id not found.", 404);
        }
        return $result;
    }

    // Generic update method
    public function update($id, $data) {
        if (!$this->dao->getById($id)) {
            throw new Exception("Cannot update. Entity with ID $id not found.", 404);
        }

        $this->dao->update($id, $data);
        return $this->dao->getById($id);
    }

    // Generic delete method
    public function delete($id) {
        if (!$this->dao->getById($id)) {
            throw new Exception("Cannot delete. Entity with ID $id not found.", 404);
        }

        $result = $this->dao->delete($id);
        if (!$result) {
            throw new Exception("Failed to delete entity with ID $id.", 500);
        }

        return ['message' => "Entity with ID $id successfully deleted."];
    }

    // Generic getAll method
    public function getAll() {
        return $this->dao->getAll();
    }
}
?>