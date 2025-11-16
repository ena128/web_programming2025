<?php
use OpenApi\Annotations as OA;

require_once __DIR__ . '/../services/TaskService.php';

Flight::set('TaskService', new TaskService());

// Get all tasks
/**
 * @OA\Get(
 *     path="/tasks",
 *     tags={"tasks"},
 *     summary="Get all tasks",
 *     @OA\Response(
 *         response=200,
 *         description="List of all tasks"
 *     )
 * )
 */
Flight::route('GET /tasks', function() {
    try {
        Flight::json(Flight::get('TaskService')->getAll());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

// Get task by ID
/**
 * @OA\Get(
 *     path="/tasks/{id}",
 *     tags={"tasks"},
 *     summary="Get task by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Task ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task found"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Task not found"
 *     )
 * )
 */
Flight::route('GET /tasks/@id', function($id){
    try {
        $task = Flight::get('TaskService')->getById($id);
        if ($task) {
            Flight::json($task);
        } else {
            Flight::json(["status" => "error", "message" => "Task not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

// Create task
/**
 * @OA\Post(
 *     path="/tasks",
 *     tags={"tasks"},
 *     summary="Create a new task",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "user_id"},
 *             @OA\Property(property="title", type="string", example="New Task"),
 *             @OA\Property(property="description", type="string", example="Task description"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2025-04-30"),
 *             @OA\Property(property="user_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Task created successfully"
 *     )
 * )
 */
Flight::route('POST /tasks', function() {
    try {
        $data = Flight::request()->data->getData();
        $task = Flight::get('TaskService')->create($data);
        Flight::json(['status' => 'success', 'task' => $task], 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

// Update task by ID
/**
 * @OA\Put(
 *     path="/tasks/{id}",
 *     tags={"tasks"},
 *     summary="Update a task by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Task ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "user_id"},
 *             @OA\Property(property="title", type="string", example="Updated Task"),
 *             @OA\Property(property="description", type="string", example="Updated task description"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2025-05-15"),
 *             @OA\Property(property="user_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task updated"
 *     )
 * )
 */
Flight::route('PUT /tasks/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        $task = Flight::get('TaskService')->update($id, $data);
        if ($task) {
            Flight::json(['status' => 'success', 'task' => $task]);
        } else {
            Flight::json(['status' => 'error', 'message' => 'Task not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

// Delete task by ID
/**
 * @OA\Delete(
 *     path="/tasks/{id}",
 *     tags={"tasks"},
 *     summary="Delete a task by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Task ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /tasks/@id', function($id) {
    try {
        $result = Flight::get('TaskService')->delete($id);
        if ($result) {
            Flight::json(['status' => 'success', 'message' => 'Task deleted']);
        } else {
            Flight::json(['status' => 'error', 'message' => 'Task not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

// Get tasks by user ID
/**
 * @OA\Get(
 *     path="/tasks/user/{user_id}",
 *     tags={"tasks"},
 *     summary="Get tasks by user ID",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of tasks for the specified user"
 *     )
 * )
 */
Flight::route('GET /tasks/user/@user_id', function($user_id) {
    try {
        $tasks = Flight::get('TaskService')->getByUserId($user_id);
        Flight::json($tasks);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});