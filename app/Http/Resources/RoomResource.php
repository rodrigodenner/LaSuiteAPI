<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * RoomResource
 *
 * This resource defines the structure of the Room response.
 * Make sure to keep this aligned with the Swagger Room schema (RoomSchemaDocumentation.php).
 */

class RoomResource extends JsonResource
{

  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'description' => $this->description,
      'slug' => $this->slug,
      'featured' => $this->featured,
      'size' => $this->size,
      'max_adults' => $this->max_adults,
      'max_children' => $this->max_children,
      'max_capacity'  => $this->max_capacity,
      'double_beds' => $this->double_beds,
      'single_beds' => $this->single_beds,
      'floor' => $this->floor,
      'type' => $this->type,
      'number' => $this->number,
      'images' => $this->images->map(function ($image) {
        return [
          'path'        => $image->image_path,
          'description' => $image->description,
          'alt'         => $image->alt,
          'featured'    => $image->featured,
        ];
      }),
      'tariffs' => $this->tariffs->map(function ($tariff) {
        return [
          'regime' => $tariff->regime->description ?? null,
          'start_date' => $tariff->start_date,
          'end_date' => $tariff->end_date,
          'type' => $tariff->type,
          'value_room' => $tariff->value_room,
          'additional_adult' => $tariff->additional_adult,
          'additional_child' => $tariff->additional_child,
        ];
      }),
    ];
  }
}
