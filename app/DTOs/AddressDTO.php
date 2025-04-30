<?php

namespace App\DTOs;

readonly class AddressDTO
{
  public function __construct(
    public string $zipcode,
    public string $state,
    public string $city,
    public string $district,
    public string $street,
    public string $number,
    public ?string $complement,
    public string $country,
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(
      zipcode: $data['zipcode'],
      state: $data['state'],
      city: $data['city'],
      district: $data['district'],
      street: $data['street'],
      number: $data['number'],
      complement: $data['complement'] ?? null,
      country: $data['country'],
    );
  }

  public function toArray(): array
  {
    return [
      'zipcode'    => $this->zipcode,
      'state'      => $this->state,
      'city'       => $this->city,
      'district'   => $this->district,
      'street'     => $this->street,
      'number'     => $this->number,
      'complement' => $this->complement,
      'country'    => $this->country,
    ];
  }
}
