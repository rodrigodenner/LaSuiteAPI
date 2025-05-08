<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Reservation extends Model
{
  use HasFactory , SoftDeletes;

  protected $fillable = [
    'checkin_at',
    'checkout_at',
    'adults',
    'children',
    'total',
  ];

  protected $casts = [
    'checkin_at'  => 'datetime',
    'checkout_at' => 'datetime',
    'created_at'  => 'datetime',
  ];


  public function guests()
  {
    return $this->belongsToMany(Guest::class)
      ->withPivot(['checkin_at', 'checkout_at', 'type']);
  }

  public function rooms(): BelongsToMany
  {
    return $this->belongsToMany(Room::class);
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class);
  }

  public function statuses(): HasMany
  {
    return $this->hasMany(ReservationStatus::class);
  }


}
