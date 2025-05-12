<?php

namespace App\Services\Reservations;

use App\Models\Guest;
use Illuminate\Http\Request;

class ReservationFilterService
{
  public function filter(Request $request)
  {
    try {
      return Guest::with([
        'reservations.rooms.tariffs.regime',
        'reservations.statuses'
      ])
        ->applyFilters($request)
        ->paginate(10);
    } catch (\Throwable $e) {
      throw $e;
    }
  }

}
