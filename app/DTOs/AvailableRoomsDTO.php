<?php

namespace App\DTOs;

use App\Http\Requests\AvailableRoomsListRequest;

readonly class AvailableRoomsDTO
{
  public function __construct(
    public string $checkin,
    public string $checkout,
    public ?int $adults,
    public ?int $children,
    public ?float $price_min,
    public ?float $price_max,
    public ?string $sort,
  ) {}

  public static function makeFromRequest(AvailableRoomsListRequest $request): self
  {
    $data = $request->validated();

    return new self(
      checkin: $data['checkin'],
      checkout: $data['checkout'],
      adults: $data['adults'] ?? null,
      children: $data['children'] ?? null,
      price_min: $data['price_min'] ?? null,
      price_max: $data['price_max'] ?? null,
      sort: $data['sort'] ?? null,
    );
  }
}
