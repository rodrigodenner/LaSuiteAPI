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

  public const STATUS_PENDING  = 'pending';
  public const STATUS_PAID     = 'paid';
  public const STATUS_FAILED   = 'failed';
  public const STATUS_CANCELED = 'canceled';

  public function reservation(): BelongsTo
  {
    return $this->belongsTo(Reservation::class);
  }
}
