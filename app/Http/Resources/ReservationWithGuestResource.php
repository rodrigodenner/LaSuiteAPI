<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReservationWithGuestResource extends JsonResource
{
  public function toArray($request): array
  {
    $checkin  = Carbon::parse($this->pivot->checkin_at ?? $this->checkin_at);
    $checkout = Carbon::parse($this->pivot->checkout_at ?? $this->checkout_at);
    $days     = $checkin->diffInDays($checkout);

    $rooms = $this->rooms->map(function ($room) use ($checkin, $checkout, $days) {
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

    $guest = $this->guests->first();

    return [
      'id'         => $this->id,
      'check_in'   => $checkin->format('Y-m-d'),
      'check_out'  => $checkout->format('Y-m-d'),
      'adults'     => $this->adults,
      'children'   => $this->children,
      'total'      => $this->total,
      'rooms'      => $rooms,
      'statuses'   => $this->statuses->map(fn($status) => [
        'status'     => $status->status,
        'created_at' => Carbon::parse($status->created_at)->format('Y-m-d H:i'),
      ]),
      'guest' => $guest ? [
        'id'           => $guest->id,
        'name'         => $guest->name,
        'birthday'     => optional($guest->birthday)->format('Y-m-d'),
        'cpf'          => $guest->cpf,
        'rg'           => $guest->rg,
        'passport'     => $guest->passport,
        'is_foreigner' => (bool) $guest->is_foreigner,
        'created_at'   => optional($guest->created_at)->toIso8601String(),
      ] : null,
    ];
  }
}
