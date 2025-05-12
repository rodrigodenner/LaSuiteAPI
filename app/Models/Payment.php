<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
  use HasFactory;

  protected $fillable = [
    'reservation_id',
    'total',
    'payment_date',
    'current_status',
    'payment_method',
    'payment_id',
    'pix_payload',
    'pix_qrcode',
    'authorization_code',
    'tid',
    'details',
  ];

  protected $casts = [
    'payment_date' => 'datetime',
    'details'      => 'array',
  ];



  public function isPaid(): bool
  {
    return $this->current_status === 'paid';
  }

  public function reservation(): BelongsTo
  {
    return $this->belongsTo(Reservation::class);
  }

  public function statuses(): HasMany
  {
    return $this->hasMany(PaymentStatus::class);
  }

  public function setDetailsAttribute($value)
  {
    $allowedKeys = ['payment_id', 'authorization_code', 'tid', 'return_code', 'return_message'];
    $filtered = array_filter($value, fn($key) => in_array($key, $allowedKeys), ARRAY_FILTER_USE_KEY);
    $this->attributes['details'] = json_encode($filtered);
  }

}
