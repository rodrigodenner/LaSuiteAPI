<?php

namespace App\DTOs;

readonly class GuestDTO
{
  public function __construct(
    public string $name,
    public string $birthday,
    public ?string $cpf,
    public ?string $rg,
    public ?string $passport,
    public bool $is_foreigner,
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(...array_merge([
      'cpf' => null,
      'rg' => null,
      'passport' => null,
    ], $data));
  }

  public function toArray(): array
  {
    return [
      'name'         => $this->name,
      'birthday'     => $this->birthday,
      'cpf'          => $this->cpf,
      'rg'           => $this->rg,
      'passport'     => $this->passport,
      'is_foreigner' => $this->is_foreigner,
    ];
  }
}

