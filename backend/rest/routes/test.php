<?php
/**
* @OA\Get(
*      path="/restaurant",
*      tags={"restaurants"},
*      summary="Get all restaurants",
*      @OA\Parameter(
*          name="location",
*          in="query",
*          required=false,
*          @OA\Schema(type="string"),
*          description="Optional location to filter restaurants"
*      ),
*      @OA\Response(
*           response=200,
*           description="Array of all restaurants in the database"
*      )
* )
*/
Flight::route('GET /restaurant', function(){
   Flight::json(["messaga"=>"hello"]);
});
