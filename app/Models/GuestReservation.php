<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GuestReservation extends Pivot
{
  public $timestamps = true;

  protected $table = 'guest_reservation';
  public $incrementing = true;

  public const TYPE_PRIMARY   = 'primary';
  public const TYPE_DEPENDENT = 'dependent';
  public const TYPE_CHILD     = 'child';
  public const TYPE_EXTERNAL  = 'external';

  protected $fillable = [
    'reservation_id',
    'guest_id',
    'type',
    'checkin_at',
    'checkout_at',
  ];

  public static function types(): array
  {
    return [
      self::TYPE_PRIMARY,
      self::TYPE_DEPENDENT,
      self::TYPE_CHILD,
      self::TYPE_EXTERNAL,
    ];
  }

}
