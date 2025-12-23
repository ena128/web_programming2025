<?php
require_once __DIR__ . '/BaseDAO.php'; 

class AuthDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('users', 'user_id'); 
    }

    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->tableName} WHERE email = :email LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Optionally add registerUser method if not present
    public function registerUser($data) {
        $sql = "INSERT INTO {$this->tableName} (email, password, role) VALUES (:email, :password, :role)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'], // hashed password
            'role' => $data['role']
        ]);
        return $this->connection->lastInsertId();
    }

    // Verify if the user exists and password matches
    public function verifyCredentials($email, $password) {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return false;
        }
        // Assuming passwords are hashed using password_hash
        if (password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    // Optionally, get all users (admin purpose)
    public function getAllUsers() {
        $stmt = $this->connection->query("SELECT user_id, name, email, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

   
}
