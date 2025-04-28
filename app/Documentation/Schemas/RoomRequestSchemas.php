<?php

namespace App\Documentation\Schemas;

/**
 * @OA\Schema(
 *   schema="RoomRequest",
 *   type="object",
 *   required={
 *     "name", "slug", "description", "featured", "size",
 *     "max_adults", "max_children", "double_beds", "single_beds",
 *     "floor", "type", "number", "tariffs", "images", "availabilities"
 *   },
 *   @OA\Property(property="name", type="string", example="Room 101"),
 *   @OA\Property(property="slug", type="string", example="room-101-test"),
 *   @OA\Property(property="description", type="string", example="Comfortable room with a sea view."),
 *   @OA\Property(property="featured", type="boolean", example=true),
 *   @OA\Property(property="size", type="string", example="40m²"),
 *   @OA\Property(property="max_adults", type="integer", example=2),
 *   @OA\Property(property="max_children", type="integer", example=1),
 *   @OA\Property(property="double_beds", type="integer", example=1),
 *   @OA\Property(property="single_beds", type="integer", example=2),
 *   @OA\Property(property="floor", type="string", example="3"),
 *   @OA\Property(property="type", type="string", example="Suite"),
 *   @OA\Property(property="number", type="string", example="101"),
 *   @OA\Property(
 *     property="tariffs",
 *     type="array",
 *     @OA\Items(
 *       required={"regime_id", "start_date", "end_date", "type", "value_room"},
 *       @OA\Property(property="regime_id", type="integer", example=1),
 *       @OA\Property(property="start_date", type="string", format="date", example="2025-05-01"),
 *       @OA\Property(property="end_date", type="string", format="date", example="2025-05-10"),
 *       @OA\Property(property="type", type="string", enum={"daily", "package"}, example="daily"),
 *       @OA\Property(property="value_room", type="number", example=300),
 *       @OA\Property(property="additional_adult", type="number", example=50),
 *       @OA\Property(property="additional_child", type="number", example=30)
 *     )
 *   ),
 *   @OA\Property(
 *     property="images",
 *     type="array",
 *     description="Send images using 'multipart/form-data'. Only one image should be marked as 'featured'.",
 *     @OA\Items(
 *       required={"file", "featured"},
 *       @OA\Property(property="file", type="string", format="binary"),
 *       @OA\Property(property="description", type="string", example="Balcony view"),
 *       @OA\Property(property="alt", type="string", example="Photo of the balcony view"),
 *       @OA\Property(property="featured", type="boolean", example=true)
 *     )
 *   ),
 *   @OA\Property(
 *     property="availabilities",
 *     type="array",
 *     @OA\Items(
 *       required={"date", "quantity"},
 *       @OA\Property(property="date", type="string", format="date", example="2025-05-01"),
 *       @OA\Property(property="quantity", type="integer", example=5)
 *     )
 *   )
 * )
 */
final class RoomRequestSchemas
{
  // Dummy class to hold the Swagger schema
}
