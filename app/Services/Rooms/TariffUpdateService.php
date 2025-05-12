<?php

namespace App\Services\Rooms;

use App\Models\Room;
use App\DTOs\UpdateTariffDTO;

class TariffUpdateService
{
  public function execute(int $roomId, array $tariffDTOs): void
  {
    $room = Room::with('tariffs')->findOrFail($roomId);

    foreach ($tariffDTOs as $dto) {
      $tariff = $room->tariffs()
        ->where('regime_id', $dto->regime_id)
        ->first();

      if (!$tariff) {
        continue;
      }

      $tariff->update([
        'value_room'       => $dto->value_room,
        'additional_adult' => $dto->additional_adult,
        'additional_child' => $dto->additional_child,
      ]);
    }

  }
}

