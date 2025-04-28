<?php

namespace App\Documentation\Schemas;

/**
 * @OA\Schema(
 *     schema="Room",
 *     type="object",
 *     title="Room",
 *     description="Schema representing the structure of the Room resource response.",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Deluxe Suite"),
 *     @OA\Property(property="description", type="string", example="Spacious room with sea view."),
 *     @OA\Property(property="slug", type="string", example="quarto-101"),
 *     @OA\Property(property="featured", type="boolean", example=true),
 *     @OA\Property(property="size", type="string", example="30m²"),
 *     @OA\Property(property="max_adults", type="integer", example=2),
 *     @OA\Property(property="max_children", type="integer", example=1),
 *     @OA\Property(property="max_capacity", type="integer", example=3),
 *     @OA\Property(property="double_beds", type="integer", example=1),
 *     @OA\Property(property="single_beds", type="integer", example=2),
 *     @OA\Property(property="floor", type="string", example="2nd"),
 *     @OA\Property(property="type", type="string", example="Deluxe"),
 *     @OA\Property(property="number", type="string", example="101"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         description="List of images related to the room, including the featured flag.",
 *         @OA\Items(
 *             @OA\Property(property="path", type="string", example="https://via.placeholder.com/300x200.png?text=Room+1"),
 *             @OA\Property(property="description", type="string", example="Main image of the room"),
 *             @OA\Property(property="alt", type="string", example="Room view"),
 *             @OA\Property(property="featured", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Property(
 *         property="tariffs",
 *         type="array",
 *         description="Tariffs and pricing details associated with the room.",
 *         @OA\Items(
 *             @OA\Property(property="regime", type="string", example="Breakfast included"),
 *             @OA\Property(property="start_date", type="string", format="date", example="2025-04-25"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2025-05-05"),
 *             @OA\Property(property="type", type="string", example="daily"),
 *             @OA\Property(property="value_room", type="number", format="float", example=200),
 *             @OA\Property(property="additional_adult", type="number", format="float", example=50),
 *             @OA\Property(property="additional_child", type="number", format="float", example=30)
 *         )
 *     )
 * )
 */
final class RoomSchemaDocumentation
{
  // Dummy class to allow Swagger to scan this file
}
