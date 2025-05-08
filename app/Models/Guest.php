<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Guest extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
//   'user_id',
   'name',
   'birthday',
   'cpf',
   'rg',
   'passport',
   'is_foreigner',
  ];


  public function addresses(): HasMany
  {
    return $this->hasMany(Address::class);
  }

  public function phones(): HasMany
  {
    return $this->hasMany(Phone::class);
  }

  public function reservations(): BelongsToMany
  {
    return $this->belongsToMany(Reservation::class)
      ->withPivot(['checkin_at', 'checkout_at','type']);
  }


  public function scopeFilterByName(Builder $query, string $name): Builder
  {
    return $query->where('name', 'like', "%{$name}%");
  }

  public function scopeFilterByCpf(Builder $query, string $cpf): Builder
  {
    return $query->where('cpf', $cpf);
  }

  public function scopeFilterByIsForeigner(Builder $query, bool $value): Builder
  {
    return $query->where('is_foreigner', $value);
  }

  public function scopeFilterByReservationCheckinBetween(Builder $query, $from, $to): Builder
  {
    try {
      $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
      $toDate = \Carbon\Carbon::parse($to)->endOfDay();
    } catch (\Exception $e) {
      return $query;
    }
    return $query->whereHas('reservations', function ($q) use ($fromDate, $toDate) {
      $q->whereBetween('guest_reservation.checkin_at', [$fromDate, $toDate]);
    });
  }

  public function scopeFilterByReservationType(Builder $query, string $type): Builder
  {
    return $query->whereHas('reservations', fn($q) => $q->where('type', $type));
  }

  public function scopeFilterByRoomId(Builder $query, int $roomId): Builder
  {
    return $query->whereHas('reservations.rooms', fn($q) => $q->where('rooms.id', $roomId));
  }

  public function scopeFilterByReservationStatus(Builder $query, string $status): Builder
  {
    return $query->whereHas('reservations.statuses', fn($q) => $q->where('status', $status));
  }

  public function scopeApplyFilters(Builder $query, Request $request): Builder
  {
    return $query
      ->when($request->filled('name'), fn($q) => $q->filterByName($request->name))
      ->when($request->filled('cpf'), fn($q) => $q->filterByCpf($request->cpf))
      ->when($request->filled('is_foreigner'), fn($q) => $q->filterByIsForeigner((bool) $request->is_foreigner))
      ->when($request->filled('checkin_from') && $request->filled('checkin_to'),
        fn($q) => $q->filterByReservationCheckinBetween($request->checkin_from, $request->checkin_to))
      ->when($request->filled('type'), fn($q) => $q->filterByReservationType($request->type))
      ->when($request->filled('room_id'), fn($q) => $q->filterByRoomId($request->room_id))
      ->when($request->filled('reservation_status'), fn($q) => $q->filterByReservationStatus($request->reservation_status));
  }

}
