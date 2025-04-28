<?php

namespace App\Services\RoomsService;

use App\DTOs\CreateRoomDTO;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RoomCreationService
{
  public function create(CreateRoomDTO $dto): Room
  {
    return DB::transaction(function () use ($dto) {
      $room = Room::create($dto->toArray());

      $this->saveImages($room, $dto->images);
      $this->saveTariffs($room, $dto->tariffs);
      $this->saveAvailabilities($room, $dto->availabilities);

      return $room->load(['images', 'tariffs.regime', 'availabilities']);
    });
  }

  private function saveImages(Room $room, array $images): void
  {
    foreach ($images as $image) {
      if ($image['file'] instanceof UploadedFile) {
        $path = $this->uploadImage($image['file'], $room->slug);
        $room->images()->create([
          'image_path'  => $path,
          'description' => $image['description'] ?? null,
          'alt'         => $image['alt'] ?? null,
          'featured'    => $image['featured'] ?? false,
        ]);
      }
    }
  }

  private function saveTariffs(Room $room, array $tariffs): void
  {
    foreach ($tariffs as $tariff) {
      $room->tariffs()->create([
        'regime_id'         => $tariff['regime_id'],
        'start_date'        => $tariff['start_date'],
        'end_date'          => $tariff['end_date'],
        'type'              => $tariff['type'],
        'value_room'        => $tariff['value_room'],
        'additional_adult'  => $tariff['additional_adult'] ?? 0,
        'additional_child'  => $tariff['additional_child'] ?? 0,
      ]);
    }
  }

  private function saveAvailabilities(Room $room, array $availabilities): void
  {
    foreach ($availabilities as $availability) {
      $room->availabilities()->create([
        'date'     => $availability['date'],
        'quantity' => $availability['quantity'],
      ]);
    }
  }

  private function uploadImage(UploadedFile $file, string $slug): string
  {
    return $file->store("rooms/{$slug}", 'public');
  }
}
