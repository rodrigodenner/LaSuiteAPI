<?php

namespace App\DTOs;

readonly class TariffDTO
{
  public function __construct(
    public int $regime_id,
    public string $start_date,
    public string $end_date,
    public string $type,
    public float $value_room,
    public float $additional_adult,
    public float $additional_child,
  ) {}

  public static function make(array $data): self
  {
    return new self(
      regime_id: $data['regime_id'],
      start_date: $data['start_date'],
      end_date: $data['end_date'],
      type: $data['type'],
      value_room: $data['value_room'],
      additional_adult: $data['additional_adult'],
      additional_child: $data['additional_child'],
    );
  }
}
