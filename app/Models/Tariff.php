<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tariff extends Model
{
  protected $fillable = [
    'regime_id',
    'room_id',
    'start_date',
    'end_date',
    'type',
    'value_room',
    'additional_adult',
    'additional_child',
  ];

  public function regime(): BelongsTo
  {
    return $this->belongsTo(Regime::class);
  }

  public function room(): BelongsTo
  {
    return $this->belongsTo(Room::class);
  }

}
