<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentStatus extends Model
{
  use HasFactory;

  public const STATUS_PENDING = 'pending';
  public const STATUS_PAID = 'paid';
  public const STATUS_FAILED = 'failed';

  protected $fillable = [
    'payment_id',
    'status',
  ];

  public function payment(): BelongsTo
  {
    return $this->belongsTo(Payment::class);
  }
}
