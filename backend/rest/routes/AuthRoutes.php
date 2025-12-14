<?php
use Firebase\JWT\JWT;
require_once __DIR__ . '/../services/AuthService.php';

Flight::group('/auth', function() {

    Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();

        if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
            Flight::halt(400, json_encode(['error' => 'Email, password, and name are required']));
        }

        // force role to ADMIN
        $data['role'] = 'ADMIN';

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

    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();

        if (empty($data['email']) || empty($data['password'])) {
            Flight::halt(400, json_encode(['error' => 'Email and password are required']));
        }

        $response = Flight::authService()->login($data);

        if ($response['success']) {
            Flight::json([
                'message' => 'User logged in successfully',
                'data' => $response['data'] // contains JWT
            ]);
        } else {
            Flight::halt(401, json_encode(['error' => $response['error']]));
        }
    });
    Flight::route('GET /auth/me', function() {
    $user = Flight::get('user'); 
    Flight::json([
        "message" => "Authenticated user",
        "user" => $user
    ]);
});
});
