<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

class UserService extends BaseService {
    public function __construct() {
        parent::__construct(new UserDAO());
    }
     // Validation logic
     private function validateUserData($data) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }

        if (strlen($data['password']) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }

        // Check for existing email
        if ($this->dao->getByEmail($data['email'])) {
            throw new Exception("Email is already in use.");
        }

        return true;
    }

    // Create a new user with validation
    public function create($data) {
        try {
            $this->validateUserData($data);  // Validacija
            $userId = $this->dao->create($data);  // Kreiranje korisnika
            return ['id' => $userId, 'message' => 'User created successfully', ];
        } catch (Exception $e) {
           
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    // Get a user by email (custom)
    public function getUserByEmail($email) {
        return $this->dao->getByEmail($email);
    }




    
}
?>