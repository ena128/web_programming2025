<?php
require_once __DIR__ . '/../DAO/AuthDAO.php';
require_once __DIR__ . '/../../config.php'; 

use Firebase\JWT\JWT;

class AuthService {
    private $authDAO;

    public function __construct() {
        $this->authDAO = new AuthDAO();
    }

    public function register($data) {
        try {
            $exists = $this->authDAO->getByEmail($data['email']);
            if ($exists) {
                return ['success' => false, 'error' => 'User already exists'];
            }

            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            
            if (!isset($data['role'])) {
                $data['role'] = 'USER';
            }

            // Koristimo createUser (ili add) iz BaseDAO
            $this->authDAO->createUser($data);

            return ['success' => true, 'message' => 'Registered successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function login($data) {
        $user = $this->authDAO->getByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        // === POPRAVKA ZA TVOJU GREŠKU (Line 56) ===
        // Provjeravamo da li baza vraća 'id' ili 'user_id'
        $db_id = isset($user['id']) ? $user['id'] : (isset($user['user_id']) ? $user['user_id'] : null);

        if (!$db_id) {
            // Ako nema ni id ni user_id, nešto nije u redu sa bazom
            return ['success' => false, 'error' => 'Database error: User ID not found'];
        }

        // Payload
        $payload = [
            'user' => [
                'id' => $db_id,  // Ovdje koristimo pronađeni ID
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];

        $jwt = JWT::encode($payload, Config::JWT_SECRET(), 'HS256');

        return ['success' => true, 'data' => ['token' => $jwt]];
    }
}
?>