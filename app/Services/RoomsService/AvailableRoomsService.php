<?php

namespace App\Services\RoomsService;

use App\DTOs\AvailableRoomsDTO;
use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AvailableRoomsService
{
  public function execute(AvailableRoomsDTO $dto): LengthAwarePaginator
  {
    if ($dto->price_min !== null && $dto->price_max !== null && $dto->price_min > $dto->price_max) {
      throw new \InvalidArgumentException('O preço mínimo não pode ser maior que o preço máximo.');
    }

    return Room::with(['images', 'tariffs.regime', 'availabilities'])
      ->availableBetween($dto->checkin, $dto->checkout)
      ->filterByPrice($dto->price_min, $dto->price_max)
      ->applySort($dto->sort)
      ->paginate(10);
  }
}
