<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class GuestWithReservationsResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id'           => $this->id,
      'name'         => $this->name,
      'birthday'     => $this->birthday instanceof \DateTimeInterface
        ? $this->birthday->format('Y-m-d')
        : (string) $this->birthday,
      'cpf'          => $this->cpf,
      'rg'           => $this->rg,
      'passport'     => $this->passport,
      'is_foreigner' => (bool) $this->is_foreigner,
      'created_at'   => $this->created_at instanceof \DateTimeInterface
        ? $this->created_at->toIso8601String()
        : (string) $this->created_at,

      'reservations' => $this->reservations->map(function ($reservation) {
        $checkin  = Carbon::parse($reservation->pivot->checkin_at);
        $checkout = Carbon::parse($reservation->pivot->checkout_at);
        $days     = $checkin->diffInDays($checkout);

        $rooms = $reservation->rooms->map(function ($room) use ($checkin, $checkout, $days) {
          $tariff = $room->tariffs
            ->firstWhere(fn($tariff) =>
              Carbon::parse($tariff->start_date)->lte($checkin) &&
              Carbon::parse($tariff->end_date)->gte($checkout)
            );

          $pricePerDay = $tariff?->value_room ?? 0;
          $subtotal    = $days * $pricePerDay;
          $regimeName  = $tariff?->regime->description ?? null;

          return [
            'id'            => $room->id,
            'name'          => $room->name,
            'slug'          => $room->slug,
            'description'   => $room->description,
            'size'          => $room->size,
            'max_adults'    => $room->max_adults,
            'max_children'  => $room->max_children,
            'double_beds'   => $room->double_beds,
            'single_beds'   => $room->single_beds,
            'floor'         => $room->floor,
            'type'          => $room->type,
            'number'        => $room->number,
            'price_per_day' => $pricePerDay,
            'regime'        => $regimeName,
            'days'          => $days,
            'subtotal'      => $subtotal,
          ];
        });

        return [
          'id'         => $reservation->id,
          'check_in'   => $checkin->format('Y-m-d'),
          'check_out'  => $checkout->format('Y-m-d'),
          'adults'     => $reservation->adults,
          'children'   => $reservation->children,
          'created_at' => $reservation->created_at instanceof \DateTimeInterface
            ? $reservation->created_at->toIso8601String()
            : (string) $reservation->created_at,
          'rooms'      => $rooms,
          'total'      => $reservation->total,
          'statuses'   => $reservation->statuses->map(function ($status) {
            return [
              'status'     => $status->status,
              'created_at' => Carbon::parse($status->created_at)->format('Y-m-d H:i'),
            ];
          }),
        ];
      }),
    ];
  }
}
