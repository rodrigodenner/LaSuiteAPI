<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regime extends Model
{
  protected $fillable = [
    'description',
    'is_active',
  ];

  public function tariffs(): HasMany
  {
    return $this->hasMany(Tariff::class);
  }

}
