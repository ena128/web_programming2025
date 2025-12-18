<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../DAO/AuthDAO.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService {

    private $authDAO;
    private $secret_key = "your_secret_key"; // Use a complex string here

    public function __construct() {
        $this->authDAO = new AuthDAO();
    }

    /**
     * Registers a new user.
     * Hashes the password and ensures the role is set.
     */
    public function register($data) {
        try {
            // Check if user already exists
            $exists = $this->authDAO->getByEmail($data['email']);
            if ($exists) {
                return ['success' => false, 'error' => 'User with this email already exists.'];
            }

            // Hash the password using PHP standard BCRYPT
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Map 'fullname' from frontend to 'name' if your DB column is named 'name'
            // If your column is 'fullname', keep it as is.
            $user = $this->authDAO->create($data);

            return [
                'success' => true, 
                'message' => 'Registration successful',
                'data' => $user
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Authenticates a user and returns a JWT token.
     */
    public function login($data) {
        try {
            // 1. Fetch user by email from DAO
            $user = $this->authDAO->getByEmail($data['email']);

            // 2. Verify existence and password
            if ($user && password_verify($data['password'], $user['password'])) {
                
                // Identify the correct ID field (handles 'id' or 'user_id')
                $user_id = isset($user['id']) ? $user['id'] : ($user['user_id'] ?? null);

                // 3. Prepare JWT Payload
                $payload = [
                    'iat' => time(),                 // Issued at
                    'exp' => time() + (60 * 60 * 8), // Expires in 8 hours
                    'user' => [
                        'id' => $user_id,
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'fullname' => $user['fullname'] ?? $user['name'] ?? 'User'
                    ]
                ];

                // 4. Encode Token
                $jwt = JWT::encode($payload, $this->secret_key, 'HS256');

                return [
                    'success' => true,
                    'data' => [
                        'token' => $jwt,
                        'user' => $payload['user']
                    ]
                ];
            }

            // If we reach here, credentials were wrong
            return ['success' => false, 'error' => 'Invalid email or password.'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Server error: ' . $e->getMessage()];
        }
    }
}