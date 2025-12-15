<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // <-- fix path
require_once __DIR__ . '/../DAO/AuthDAO.php';

use Firebase\JWT\JWT;




class AuthService {

    private $authDAO;

    public function __construct() {
        $this->authDAO = new AuthDAO();
    }

    // REGISTER
    public function register($data) {
        try {
            // hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $user = $this->authDAO->create($data);
            return ['success' => true, 'data' => $user];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // LOGIN
    public function login($data) {
        $user = $this->authDAO->getByEmail($data['email']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $payload = [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'exp' => time() + 3600 // token expires in 1 hour
            ];
            $jwt = JWT::encode($payload, 'your_secret_key', 'HS256');
            return ['success' => true, 'data' => ['token' => $jwt]];
        }
        return ['success' => false, 'error' => 'Invalid credentials'];
    }
}
