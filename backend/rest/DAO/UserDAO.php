<?php
require_once __DIR__ . '/BaseDAO.php';


class UserDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("users", "user_id"); 
    }

   
    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createUser($name, $email, $password, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $this->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }

    
    public function getAllUsers() {
        return $this->fetchAll("SELECT * FROM users");
    }

   
    public function updateUser($user_id, $name, $email, $role) {
        return $this->update($user_id, [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ]);
    }

    
    public function updatePassword($user_id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($user_id, ['password' => $hashedPassword]);
    }

   
    public function deleteUser($user_id) {
        return $this->delete($user_id);
    }
}
?>