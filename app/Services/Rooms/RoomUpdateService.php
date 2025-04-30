<?php

namespace App\Services\Rooms;

use App\DTOs\UpdateRoomDTO;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class RoomUpdateService
{
  public function update(Room $room, UpdateRoomDTO $dto): Room
  {
    return DB::transaction(function () use ($room, $dto) {
      $updateData = [];

      foreach ([
                 'name', 'slug', 'description', 'featured', 'size',
                 'max_adults', 'max_children', 'double_beds', 'single_beds',
                 'floor', 'type', 'number'
               ] as $field) {
        if (!is_null($dto->{$field})) {
          $updateData[$field] = $dto->{$field};
        }
      }

      if ($updateData) {
        $room->update($updateData);
      }

      if (!is_null($dto->images)) {
        $room->images()->delete();
        $this->saveImages($room, $dto->images);
      }

      if (!is_null($dto->tariffs)) {
        $room->tariffs()->delete();
        $this->saveTariffs($room, $dto->tariffs);
      }

      if (!is_null($dto->availabilities)) {
        $room->availabilities()->delete();
        $this->saveAvailabilities($room, $dto->availabilities);
      }

      return $room->fresh()->load(['images', 'tariffs.regime', 'availabilities']);
    });
  }

  private function saveImages(Room $room, array $images): void
  {
    foreach ($images as $image) {
      if ($image['file']) {
        $path = $this->uploadImage($image['file'], $room->slug);
        $room->images()->create([
          'image_path' => $path,
          'description' => $image['description'] ?? null,
          'alt' => $image['alt'] ?? null,
          'featured' => $image['featured'] ?? false,
        ]);
      }
    }
  }

  private function saveTariffs(Room $room, array $tariffs): void
  {
    foreach ($tariffs as $tariff) {
      $room->tariffs()->create($tariff);
    }
  }

  private function saveAvailabilities(Room $room, array $availabilities): void
  {
    foreach ($availabilities as $availability) {
      $room->availabilities()->create($availability);
    }
  }

  private function uploadImage($file, string $slug): string
  {
    return $file->store("rooms/{$slug}", 'public');
  }
}

