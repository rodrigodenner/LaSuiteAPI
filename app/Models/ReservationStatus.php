<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationStatus extends Model
{
  public $timestamps = false;

  protected $fillable = [
    'reservation_id',
    'status',
    'created_at',
  ];

  public function reservation(): BelongsTo
  {
    return $this->belongsTo(Reservation::class);
  }
}
