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
        $stmt = $this->connection->query("SELECT id, name, email, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Create a new user
    public function createUser($name, $email, $password, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":role", $role);
        $stmt->execute();
        return $this->connection->lastInsertId();
    }
}
