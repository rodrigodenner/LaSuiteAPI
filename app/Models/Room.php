<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Room extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'slug',
    'featured',
    'description',
    'size',
    'max_adults',
    'max_children',
    'double_beds',
    'single_beds',
    'floor',
    'type',
    'number',
  ];

  public function reservations(): BelongsToMany
  {
    return $this->belongsToMany(Reservation::class);
  }

  public function images(): HasMany
  {
    return $this->hasMany(RoomImage::class);
  }

  public function tariffs(): HasMany
  {
    return $this->hasMany(Tariff::class);
  }

  public function availabilities(): HasMany
  {
    return $this->hasMany(Availability::class);
  }

  public function getMaxCapacityAttribute(): int
  {
    return $this->max_adults + $this->max_children;
  }


  public function setSlugAttribute($value)
  {
    $this->attributes['slug'] = Str::slug($value);
  }


  /**
   * @method static Builder|Room availableBetween(string $checkin, string $checkout)
   * @method static Builder|Room filterByPrice(?float $priceMin, ?float $priceMax)
   * @method static Builder|Room applySort(?string $sort)
   */
  public function scopeAvailableBetween(Builder $query, string $checkin, string $checkout)
  {
    return $query->whereHas('availabilities', function ($q) use ($checkin, $checkout) {
      $q->whereBetween('date', [$checkin, $checkout])
        ->where('quantity', '>', 0);
    });
  }

  public function scopeFilterByPrice(Builder $query, ?float $priceMin, ?float $priceMax)
  {
    return $query
      ->when($priceMin, function ($q) use ($priceMin) {
        $q->whereHas('tariffs', function ($subQ) use ($priceMin) {
          $subQ->where('value_room', '>=', $priceMin);
        });
      })
      ->when($priceMax, function ($q) use ($priceMax) {
        $q->whereHas('tariffs', function ($subQ) use ($priceMax) {
          $subQ->where('value_room', '<=', $priceMax);
        });
      });
  }

  public function scopeApplySort(Builder $query, ?string $sort)
  {
    if (in_array($sort, ['price_asc', 'price_desc'], true)) {
      $query->addSelect([
        'min_price' => \DB::table('tariffs')
          ->selectRaw('MIN(value_room)')
          ->whereColumn('tariffs.room_id', 'rooms.id')
      ]);

      $direction = $sort === 'price_asc' ? 'asc' : 'desc';
      return $query->orderBy('min_price', $direction);
    }

    if ($sort === 'title_asc') {
      return $query->orderBy('name', 'asc');
    }

    if ($sort === 'title_desc') {
      return $query->orderBy('name', 'desc');
    }

    return $query;
  }


}
