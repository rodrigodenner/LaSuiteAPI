<?php

namespace App\DTOs;

readonly class AvailabilityDTO
{
  public function __construct(
    public string $date,
    public int $quantity
  ) {}

  public static function make(array $data): self
  {
    return new self(
      date: $data['date'],
      quantity: $data['quantity']
    );
  }
}
