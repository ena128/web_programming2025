<?php
use OpenApi\Annotations as OA;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Pomoćna funkcija za provjeru da li je korisnik ADMIN.
 * Ako nije admin, zaustavlja izvršavanje i vraća 403.
 */
function requireAdmin() {
    $authHeader = Flight::request()->getHeader('Authorization');
    if (!$authHeader) {
        Flight::halt(401, json_encode(['error' => 'Token not provided']));
    }

    $tokenParts = explode(' ', $authHeader);
    if (count($tokenParts) < 2) {
        Flight::halt(401, json_encode(['error' => 'Invalid token format']));
    }
    $token = $tokenParts[1];

    try {
        // Dekodiramo token koristeći isti Secret Key iz Config-a
        $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
        
        // Provjera role
        if ($decoded->user->role != 'ADMIN') {
            Flight::halt(403, json_encode(['error' => 'Access denied. Admin role required.']));
        }
    } catch (\Exception $e) {
        Flight::halt(401, json_encode(['error' => 'Invalid token: ' . $e->getMessage()]));
    }
}

/**
 * @OA\Get(
 * path="/users",
 * tags={"users"},
 * summary="Get all users (Admin Only)",
 * security={{"bearerAuth": {}}},
 * @OA\Response(response=200, description="List of all users"),
 * @OA\Response(response=403, description="Access denied")
 * )
 */
Flight::route('GET /users', function () {
    // 1. Sigurnosna provjera: SAMO ADMIN
    requireAdmin();

    try {
        Flight::json(Flight::userService()->getAll());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 * path="/users/{id}",
 * tags={"users"},
 * summary="Get user by ID (Admin Only)",
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="ID of the user",
 * @OA\Schema(type="integer", example=1)
 * ),
 * @OA\Response(response=200, description="User found"),
 * @OA\Response(response=404, description="User not found")
 * )
 */
Flight::route('GET /users/@id', function ($id) {
    requireAdmin(); // Možeš ukloniti ako želiš da useri vide jedni druge

    try {
        $user = Flight::userService()->getById($id);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::json(['error' => 'User not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 * path="/users",
 * tags={"users"},
 * summary="Create a new user (Admin Only)",
 * security={{"bearerAuth": {}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"name", "email", "password", "role"},
 * @OA\Property(property="name", type="string", example="Ena"),
 * @OA\Property(property="email", type="string", example="ena@example.com"),
 * @OA\Property(property="password", type="string", example="password123"),
 * @OA\Property(property="role", type="string", example="USER")
 * )
 * ),
 * @OA\Response(response=201, description="User created successfully")
 * )
 */
Flight::route('POST /users', function () {
    requireAdmin();

    try {
        $data = Flight::request()->data->getData();
        $newUser = Flight::userService()->add($data); // Koristimo 'add' ili 'create' zavisno od tvoje BaseDAO
        Flight::json($newUser, 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 * path="/users/{id}",
 * tags={"users"},
 * summary="Update a user by ID (Admin Only)",
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="User ID",
 * @OA\Schema(type="integer", example=1)
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"name", "email", "role"},
 * @OA\Property(property="name", type="string", example="Updated Ena"),
 * @OA\Property(property="email", type="string", example="updated@example.com"),
 * @OA\Property(property="role", type="string", example="ADMIN")
 * )
 * ),
 * @OA\Response(response=200, description="User updated")
 * )
 */
Flight::route('PUT /users/@id', function ($id) {
    requireAdmin();

    try {
        $data = Flight::request()->data->getData();
        $updatedUser = Flight::userService()->update($id, $data);
        if ($updatedUser) {
            Flight::json(['status' => 'success', 'message' => 'User updated successfully', 'data' => $updatedUser]);
        } else {
            Flight::json(['status' => 'error', 'message' => 'Failed to update user'], 400);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 * path="/users/{id}",
 * tags={"users"},
 * summary="Delete a user by ID (Admin Only)",
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="User ID",
 * @OA\Schema(type="integer", example=1)
 * ),
 * @OA\Response(response=200, description="User deleted successfully")
 * )
 */
Flight::route('DELETE /users/@id', function ($id) {
    requireAdmin();

    try {
        $result = Flight::userService()->delete($id);
        if ($result) {
            Flight::json(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            Flight::json(['status' => 'error', 'message' => 'Failed to delete user'], 400);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});