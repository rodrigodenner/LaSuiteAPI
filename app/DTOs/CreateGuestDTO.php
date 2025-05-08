<?php

namespace App\DTOs;

use App\Http\Requests\ReservationStoreRequest;

readonly class CreateGuestDTO
{
  public function __construct(
    public GuestDTO $guest,
    public AddressDTO $address,
    public array $phones,
    public ReservationDTO $reservation,
  ) {}

  public static function makeFromRequest(ReservationStoreRequest $request): self
  {
    $data = $request->validated();

    return new self(
      guest: GuestDTO::fromArray($data['guest']),
      address: AddressDTO::fromArray($data['addresses'][0]),
      phones: collect($data['phones'])
        ->map(fn ($p) => PhoneDTO::fromArray($p))
        ->toArray(),
      reservation: ReservationDTO::fromArray($data['reservation']),
    );
  }
}
