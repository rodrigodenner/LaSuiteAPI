<?php

namespace App\DTOs;

readonly class PhoneDTO
{
  public function __construct(
    public string $phone_number,
    public string $type,
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(...$data);
  }

  public function toArray(): array
  {
    return [
      'phone_number' => $this->phone_number,
      'type'   => $this->type,
    ];
  }
}

