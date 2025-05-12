<?php

namespace App\Services\Reservations;

use App\Models\PaymentStatus;
use App\Models\Reservation;

class ReservationPaymentService
{
  public function createInitialPayment(Reservation $reservation, float $total): void
  {
    $payment = $reservation->payments()->create([
      'total'          => $total,
      'payment_date'   => now(),
      'current_status' => PaymentStatus::STATUS_PENDING,
    ]);

    $payment->statuses()->create([
      'status' => PaymentStatus::STATUS_PENDING,
    ]);
  }
}
