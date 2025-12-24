<?php
use Firebase\JWT\JWT;

require_once __DIR__ . '/../services/AuthService.php';

Flight::group('/auth', function() {

    /**
     * POST /auth/register
     * Handles user registration and forces role to ADMIN.
     */
    Flight::route('POST /register', function() {
        // Try to get data from Flight's request object
        $data = Flight::request()->data->getData();

        // If data is empty (happens with some JSON payloads), manually parse body
        if (empty($data)) {
            $data = json_decode(Flight::request()->getBody(), true);
        }

        if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
            Flight::halt(400, json_encode(['error' => 'Name, email, and password are required']));
        }

        // Force role to ADMIN for this project setup
        $data['role'] = 'USER';

        $response = Flight::authService()->register($data);

        if ($response['success']) {
            Flight::json([
                'message' => 'User registered successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, json_encode(['error' => $response['error']]));
        }
    });

    /**
     * POST /auth/login
     * Handles user login and returns a JWT token.
     */
    Flight::route('POST /login', function() {
        // Try to get data from Flight's request object
        $data = Flight::request()->data->getData();

        // Ensure JSON body is parsed correctly even if headers vary
        if (empty($data)) {
            $data = json_decode(Flight::request()->getBody(), true);
        }

        if (empty($data['email']) || empty($data['password'])) {
            Flight::halt(400, json_encode(['error' => 'Email and password are required']));
        }

        $response = Flight::authService()->login($data);

        if ($response['success']) {
            Flight::json([
                'message' => 'User logged in successfully',
                'data' => $response['data'] // contains JWT and user info
            ]);
        } else {
            // This returns the 401 Unauthorized error with the message from AuthService
            Flight::halt(401, json_encode(['error' => $response['error']]));
        }
    });

    /**
     * GET /auth/me
     * Returns details of the currently authenticated user.
     */
    Flight::route('GET /me', function() {
        // This 'user' is usually set by a Middleware after verifying the JWT
        $user = Flight::get('user'); 
        
        if (!$user) {
            Flight::halt(403, json_encode(['error' => 'Unauthorized access']));
        }

        Flight::json([
            "message" => "Authenticated user",
            "user" => $user
        ]);
    });

});