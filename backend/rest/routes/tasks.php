<?php
use OpenApi\Annotations as OA;

Flight::set('taskService', new TaskService());

/**
 * @OA\Get(
 * path="/tasks",
 * tags={"tasks"},
 * summary="Get tasks (Admin sees all, User sees own)",
 * @OA\Response(response=200, description="List of tasks")
 * )
 */
Flight::route('GET /tasks', function(){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    $user = Flight::get('user'); // Podaci iz dekodiranog tokena

    try {
        if (strtolower($user->role) === Roles::ADMIN) {
            // Admin poziva getAll da vidi sve u sistemu
            Flight::json(Flight::taskService()->getAll());
        } else {
            // Običan korisnik poziva metodu koja filtrira po njegovom ID-u
            Flight::json(Flight::taskService()->getByUserId($user->id));
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(['error' => $e->getMessage()]));
    }
});

/**
 * @OA\Get(
 * path="/tasks/{id}",
 * tags={"tasks"},
 * summary="Get task by ID"
 * )
 */
Flight::route('GET /tasks/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    
    // Ovdje bi idealno trebala provjera da li task pripada useru, 
    // ali za sada samo vraćamo task po ID-u.
    Flight::json(Flight::taskService()->getById($id));
});

/**
 * @OA\Post(
 * path="/tasks",
 * tags={"tasks"},
 * summary="Create a task"
 * )
 */
Flight::route('POST /tasks', function(){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $data = Flight::request()->data->getData();
    
    $user = Flight::get('user');
    $data['user_id'] = $user->id;

    // Pozivamo metodu za kreiranje (create ili add, zavisno od servisa)
    Flight::json(Flight::taskService()->create($data));
});

/**
 * @OA\Put(
 * path="/tasks/{id}",
 * tags={"tasks"},
 * summary="Update a task"
 * )
 */
Flight::route('PUT /tasks/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $data = Flight::request()->data->getData();
    Flight::json(Flight::taskService()->update($id, $data));
});

/**
 * @OA\Delete(
 * path="/tasks/{id}",
 * tags={"tasks"},
 * summary="Delete a task"
 * )
 */
Flight::route('DELETE /tasks/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::taskService()->delete($id));
});

/**
 * @OA\Get(
 * path="/tasks/user/{user_id}",
 * tags={"tasks"},
 * summary="Get tasks by user ID"
 * )
 */
Flight::route('GET /tasks/user/@user_id', function($user_id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::taskService()->getByUserId($user_id));
});