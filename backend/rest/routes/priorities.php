
<?php
use OpenApi\Annotations as OA;
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../services/PriorityService.php';

Flight::set('priorityService', new PriorityService());

/**
 * @OA\Get(
 *     path="/priorities",
 *     tags={"priorities"},
 *     summary="Get all priorities",
 *     @OA\Response(
 *         response=200,
 *         description="List of all priorities"
 *     )
 * )
 */
Flight::route('GET /priorities', function() {
    Flight::json(Flight::get('priorityService')->getAll());
});

/**
 * @OA\Get(
 *     path="/priorities/{id}",
 *     tags={"priorities"},
 *     summary="Get priority by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Priority ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority found"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Priority not found"
 *     )
 * )
 */
Flight::route('GET /priorities/@id', function($id) {
    Flight::json(Flight::get('priorityService')->getById($id));
});

/**
 * @OA\Post(
 *     path="/priorities",
 *     tags={"priorities"},
 *     summary="Create a new priority",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="High"),
 *             @OA\Property(property="color", type="string", example="#FF0000", default="#FF0000")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority created successfully"
 *     )
 * )
 */


// create a new priority
Flight::route('POST /priorities', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('priorityService')->create($data));
});


/**
 * @OA\Put(
 *     path="/priorities/{id}",
 *     tags={"priorities"},
 *     summary="Update a priority",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Priority ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Updated Priority")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority updated successfully"
 *     )
 * )
 */
Flight::route('PUT /priorities/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('priorityService')->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/priorities/{id}",
 *     tags={"priorities"},
 *     summary="Delete a priority",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Priority ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /priorities/@id', function($id) {
    Flight::json(Flight::get('priorityService')->delete($id));
});
?>
