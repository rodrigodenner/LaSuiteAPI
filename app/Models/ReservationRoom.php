<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ReservationRoom extends Pivot
{
  protected $table = 'reservation_room';
  public $incrementing = true;

  protected $fillable = [
    'reservation_id',
    'room_id',
  ];


}
