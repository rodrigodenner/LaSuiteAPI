<?php

namespace App\Services\Guests;

use App\DTOs\CreateGuestDTO;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use Illuminate\Support\Facades\DB;

class GuestCreateService
{
  public function execute(CreateGuestDTO $dto): Guest
  {
    return DB::transaction(function () use ($dto) {
      $guest = $this->createOrUpdateGuest($dto);
      $this->addAddress($guest, $dto);
      $this->addPhones($guest, $dto);
      $this->ensureNoDateConflict($guest, $dto);

      $reservation = $this->createReservation($dto);
      $this->attachGuestReservation($guest, $reservation, $dto);
      $this->attachRoom($reservation, $dto);
      $this->setInitialStatus($reservation);

      return $guest;
    });
  }

  protected function createOrUpdateGuest(CreateGuestDTO $dto): Guest
  {
    $query = Guest::query();

    if ($dto->guest->is_foreigner) {
      return $query->firstOrCreate(
        ['passport' => $dto->guest->passport],
        $dto->guest->toArray()
      );
    }

    $existing = $query->where(function ($q) use ($dto) {
      $q->where('cpf', $dto->guest->cpf)
        ->orWhere('rg', $dto->guest->rg);
    })->first();

    return $existing ?? Guest::create($dto->guest->toArray());
  }

  protected function addAddress(Guest $guest, CreateGuestDTO $dto): void
  {
    $guest->addresses()->firstOrCreate(
      ['zipcode' => $dto->address->zipcode, 'street' => $dto->address->street],
      $dto->address->toArray()
    );
  }

  protected function addPhones(Guest $guest, CreateGuestDTO $dto): void
  {
    foreach ($dto->phones as $phoneDTO) {
      $guest->phones()->firstOrCreate(
        ['phone_number' => $phoneDTO->phone_number],
        $phoneDTO->toArray()
      );
    }
  }

  protected function ensureNoDateConflict(Guest $guest, CreateGuestDTO $dto): void
  {
    $conflict = $guest->reservations()
      ->wherePivot('checkin_at', $dto->reservation->checkin_at)
      ->wherePivot('checkout_at', $dto->reservation->checkout_at)
      ->exists();

    if ($conflict) {
      throw new \Exception('Já existe uma reserva para este hóspede neste mesmo período.');
    }
  }

  protected function createReservation(CreateGuestDTO $dto): Reservation
  {
    return Reservation::create($dto->reservation->toArray());
  }

  protected function attachGuestReservation(Guest $guest, Reservation $reservation, CreateGuestDTO $dto): void
  {
    $guest->reservations()->attach([
      $reservation->id => [
        'type'        => $dto->reservation->type,
        'checkin_at'  => $dto->reservation->checkin_at,
        'checkout_at' => $dto->reservation->checkout_at,
      ]
    ]);
  }

  protected function attachRoom(Reservation $reservation, CreateGuestDTO $dto): void
  {
    $reservation->rooms()->attach($dto->reservation->room_id);
  }

  protected function setInitialStatus(Reservation $reservation): void
  {
    $reservation->statuses()->create([
      'status' => ReservationStatus::STATUS_PENDING,
    ]);
  }
}
