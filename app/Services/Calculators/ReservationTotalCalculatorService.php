<?php

namespace App\Services\Calculators;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReservationTotalCalculatorService
{
  public function calculate(
    Room $room,
    string $checkin_at,
    string $checkout_at,
    int $adults,
    int $children,
    int $regimeId = null
  ): float {
    $checkin = Carbon::parse($checkin_at);
    $checkout = Carbon::parse($checkout_at);
    $days = $checkin->diffInDays($checkout);

    $tariff = $room->tariffs->firstWhere(function ($tariff) use ($checkin, $checkout, $regimeId) {
      $withinDates = Carbon::parse($tariff->start_date)->lte($checkin) &&
        Carbon::parse($tariff->end_date)->gte($checkout);

      $regimeMatch = $regimeId !== null
        ? $tariff->regime_id == $regimeId
        : true;

      return $withinDates && $regimeMatch;
    });

    if (!$tariff) {
      throw new \Exception('Nenhuma tarifa válida encontrada para o período e regime selecionado.');
    }

    $basePrice = (float) $tariff->value_room;
    $extraAdults = max(0, $adults - $room->max_adults);
    $extraChildren = max(0, $children - $room->max_children);

    $additionalAdult = $extraAdults * (float) $tariff->additional_adult;
    $additionalChild = $extraChildren * (float) $tariff->additional_child;

    $dailyTotal = $basePrice + $additionalAdult + $additionalChild;

    return $days * $dailyTotal;
  }
}
