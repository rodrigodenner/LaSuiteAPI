<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
  protected $fillable = [
    'room_id',
    'date',
    'quantity',
  ];

  public function room(): BelongsTo
  {
    return $this->belongsTo(Room::class);
  }

}
