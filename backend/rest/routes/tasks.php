<?php
use OpenApi\Annotations as OA;

require_once __DIR__ . '/../services/TaskService.php';


Flight::set('TaskService', new TaskService());

/**
 * @OA\Get(
 *     path="/tasks",
 *     tags={"tasks"},
 *     summary="Get all tasks",
 *     @OA\Response(response=200, description="List of tasks")
 * )
 */
Flight::route('GET /tasks', function(){
    Flight::auth_middleware()->authorizeRole(Roles::USER);

    Flight::json(Flight::TaskService()->getAll());
});


/**
 * @OA\Get(
 *     path="/tasks/{id}",
 *     tags={"tasks"},
 *     summary="Get task by ID"
 * )
 */
Flight::route('GET /tasks/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::USER);

    Flight::json(Flight::TaskService()->getById($id));
});


/**
 * @OA\Post(
 *     path="/tasks",
 *     tags={"tasks"},
 *     summary="Create a task"
 * )
 */
Flight::route('POST /tasks', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $data = Flight::request()->data->getData();
    Flight::json(Flight::TaskService()->create($data));
});


/**
 * @OA\Put(
 *     path="/tasks/{id}",
 *     tags={"tasks"},
 *     summary="Update a task"
 * )
 */
Flight::route('PUT /tasks/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $data = Flight::request()->data->getData();
    Flight::json(Flight::TaskService()->update($id, $data));
});


/**
 * @OA\Delete(
 *     path="/tasks/{id}",
 *     tags={"tasks"},
 *     summary="Delete a task"
 * )
 */
Flight::route('DELETE /tasks/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    Flight::json(Flight::TaskService()->delete($id));
});


/**
 * @OA\Get(
 *     path="/tasks/user/{user_id}",
 *     tags={"tasks"},
 *     summary="Get tasks by user ID"
 * )
 */
Flight::route('GET /tasks/user/@user_id', function($user_id){
    Flight::auth_middleware()->authorizeRole(Roles::USER);

    Flight::json(Flight::TaskService()->getByUserId($user_id));
});
