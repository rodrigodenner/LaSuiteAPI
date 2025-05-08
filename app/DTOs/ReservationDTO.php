<?php

namespace App\DTOs;

readonly class ReservationDTO
{
  public function __construct(
    public string $checkin_at,
    public string $checkout_at,
    public int $adults,
    public int $children,
    public int $room_id,
    public string $type,
    public ?int $regime_id = null,
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(...$data);
  }

  public function toArray(): array
  {
    return [
      'checkin_at'  => $this->checkin_at,
      'checkout_at' => $this->checkout_at,
      'adults'      => $this->adults,
      'children'    => $this->children,
      'room_id'     => $this->room_id,
      'regime_id'   => $this->regime_id,
    ];
  }

}

