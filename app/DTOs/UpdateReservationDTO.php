<?php

namespace App\DTOs;

use App\Http\Requests\UpdateReservationRequest;

readonly class UpdateReservationDTO
{
  public function __construct(
    public ?string $checkin_at,
    public ?string $checkout_at,
    public ?int $adults,
    public ?int $children,
    public ?int $room_id,
    public ?string $type,
    public ?int $regime_id,
  ) {}

  public static function makeFromRequest(UpdateReservationRequest $request): self
  {
    $data = $request->validated()['reservation'] ?? [];

    return new self(
      checkin_at: $data['checkin_at'] ?? null,
      checkout_at: $data['checkout_at'] ?? null,
      adults: $data['adults'] ?? null,
      children: $data['children'] ?? null,
      room_id: $data['room_id'] ?? null,
      type: $data['type'] ?? null,
      regime_id: $data['regime_id'] ?? null,
    );
  }

  public function toArray(): array
  {
    return [
      'checkin_at'  => $this->checkin_at,
      'checkout_at' => $this->checkout_at,
      'adults'      => $this->adults,
      'children'    => $this->children,
      'room_id'     => $this->room_id,
      'type'        => $this->type,
      'regime_id'   => $this->regime_id,
    ];
  }
}
