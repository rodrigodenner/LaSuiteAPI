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
      'max_capacity' => $this->max_capacity,
      'double_beds' => $this->double_beds,
      'single_beds' => $this->single_beds,
      'floor' => $this->floor,
      'type' => $this->type,
      'number' => $this->number,
      'images' => $this->images->map(function ($image) {
        return [
          'path' => $image->image_path,
          'description' => $image->description,
          'alt' => $image->alt,
          'featured' => (bool) $image->featured,
        ];
      }),
      'tariffs' => $this->tariffs->map(function ($tariff) {
        return [
          'regime_id' => $tariff->regime_id,
          'regime' => optional($tariff->regime)->description,
          'start_date' => $tariff->start_date,
          'end_date' => $tariff->end_date,
          'type' => $tariff->type,
          'value_room' => number_format($tariff->value_room, 2, '.', ''),
          'additional_adult' => number_format($tariff->additional_adult, 2, '.', ''),
          'additional_child' => number_format($tariff->additional_child, 2, '.', ''),
        ];
      }),
    ];
  }
}
