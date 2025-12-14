<?php

require_once __DIR__ . '/../services/CategoryService.php';

Flight::set('categoryService', new CategoryService());

/**
 * @OA\Get(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Get all categories",
 *     @OA\Response(
 *         response=200,
 *         description="List of all categories"
 *     )
 * )
 */
Flight::route('GET /categories', function() {
    Flight::auth_middleware()->authorizeRole(Roles::USER);

    Flight::json(Flight::categoryService()->getAll());
});


/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Category found"),
 *     @OA\Response(response=404, description="Category not found")
 * )
 */
Flight::route('GET /categories/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::USER);

    $category = Flight::categoryService()->getById($id);

    if ($category) {
        Flight::json($category);
    } else {
        Flight::json(["message" => "Category not found"], 404);
    }
});


/**
 * @OA\Post(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Create a new category",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Work"),
 *             @OA\Property(property="id", type="integer", example=7)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category created successfully")
 * )
 */
Flight::route('POST /categories', function() {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $data = Flight::request()->data->getData();
    Flight::json(Flight::categoryService()->create($data));
});


/**
 * @OA\Put(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Update a category",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="School")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully"
 *     )
 * )
 */
Flight::route('PUT /categories/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $data = Flight::request()->data->getData();
    Flight::json(Flight::categoryService()->update($id, $data));
});


/**
 * @OA\Delete(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Delete a category",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /categories/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    Flight::json(Flight::categoryService()->delete($id));
});

?>
