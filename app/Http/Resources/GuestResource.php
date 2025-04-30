<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
{
  public function toArray($request): array
  {
    $latestReservation = $this->reservations->sortByDesc('pivot.checkin_at')->first();
    $room = $latestReservation?->rooms->first();
    $status = $latestReservation?->statuses->sortByDesc('created_at')->first();

    return [
      'id'           => $this->id,
      'name'         => $this->name,
      'birthday'     => $this->birthday,
      'cpf'          => $this->cpf,
      'rg'           => $this->rg,
      'passport'     => $this->passport,
      'is_foreigner' => $this->is_foreigner,

      'latest_reservation' => $latestReservation ? [
        'id'          => $latestReservation->id,
        'checkin_at'  => $latestReservation->pivot->checkin_at,
        'checkout_at' => $latestReservation->pivot->checkout_at,
        'adults'   => $latestReservation->adults,
        'children' => $latestReservation->children,
        'status'      => $status?->status,
        'room'        => $room ? [
          'id'           => $room->id,
          'name'         => $room->name,
          'slug'         => $room->slug,
          'featured'     => $room->featured,
          'description'  => $room->description,
          'size'         => $room->size,
          'max_adults'   => $room->max_adults,
          'max_children' => $room->max_children,
          'double_beds'  => $room->double_beds,
          'single_beds'  => $room->single_beds,
          'floor'        => $room->floor,
          'type'         => $room->type,
          'number'       => $room->number,
        ] : null,
      ] : null,

      'created_at' => $this->created_at->toDateTimeString(),
    ];
  }
}
