<?php

namespace App\DTOs;

readonly class UpdateTariffDTO
{
  public function __construct(
    public int $regime_id,
    public float $value_room,
    public float $additional_adult,
    public float $additional_child,
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(
      regime_id: $data['regime_id'],
      value_room: $data['value_room'],
      additional_adult: $data['additional_adult'],
      additional_child: $data['additional_child'],
    );
  }
}
