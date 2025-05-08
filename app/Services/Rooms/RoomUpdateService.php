<?php

namespace App\Services\Rooms;

use App\DTOs\UpdateRoomDTO;
use App\DTOs\ImageDTO;
use App\DTOs\TariffDTO;
use App\DTOs\AvailabilityDTO;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

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
    foreach ($images as $imageDTO) {
      if (! $imageDTO instanceof ImageDTO) {
        continue;
      }

      if ($imageDTO->file instanceof UploadedFile) {
        $path = $this->uploadImage($imageDTO->file, $room->slug);

        $room->images()->create([
          'image_path'  => $path,
          'description' => $imageDTO->description,
          'alt'         => $imageDTO->alt,
          'featured'    => $imageDTO->featured,
        ]);
      }
    }
  }

  private function saveTariffs(Room $room, array $tariffs): void
  {
    foreach ($tariffs as $tariffDTO) {
      if (! $tariffDTO instanceof TariffDTO) {
        continue;
      }

      $room->tariffs()->create([
        'regime_id'         => $tariffDTO->regime_id,
        'start_date'        => $tariffDTO->start_date,
        'end_date'          => $tariffDTO->end_date,
        'type'              => $tariffDTO->type,
        'value_room'        => $tariffDTO->value_room,
        'additional_adult'  => $tariffDTO->additional_adult,
        'additional_child'  => $tariffDTO->additional_child,
      ]);
    }
  }

  private function saveAvailabilities(Room $room, array $availabilities): void
  {
    foreach ($availabilities as $availabilityDTO) {
      if (! $availabilityDTO instanceof AvailabilityDTO) {
        continue;
      }

      $room->availabilities()->create([
        'date'     => $availabilityDTO->date,
        'quantity' => $availabilityDTO->quantity,
      ]);
    }
  }

  private function uploadImage(UploadedFile $file, string $slug): string
  {
    return $file->store("rooms/{$slug}", 'public');
  }
}
