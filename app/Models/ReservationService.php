<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ReservationService extends Pivot
{
  protected $table = 'reservation_service';
  public $incrementing = true;

  protected $fillable = [
    'reservation_id',
    'service_id',
  ];
}
