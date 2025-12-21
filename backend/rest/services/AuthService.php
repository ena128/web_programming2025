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
            // 1. Provjera da li korisnik postoji
            $exists = $this->authDAO->getByEmail($data['email']);
            if ($exists) {
                return ['success' => false, 'error' => 'User with this email already exists'];
            }

            // 2. Hashiranje lozinke
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // 3. Postavljanje role
            if (!isset($data['role'])) {
                $data['role'] = 'USER';
            }

            // 4. Upis u bazu
            // createUser vraća ID novog korisnika
            $new_user_id = $this->authDAO->createUser($data);

            // 5. Priprema odgovora (OVO JE FALILO)
            // Dodajemo ID u podatke koje vraćamo
            $data['id'] = $new_user_id;
            
            // Brišemo password iz odgovora radi sigurnosti
            unset($data['password']);

            return [
                'success' => true, 
                'message' => 'User registered successfully',
                'data' => $data // <--- OVO JE KLJUČNO! Sada ruta neće pucati.
            ];

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