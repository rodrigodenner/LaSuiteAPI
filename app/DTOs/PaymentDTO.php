<?php

namespace App\DTOs;

use App\Http\Requests\PaymentRequest;
use App\Payments\Enums\BillingTypeEnum;

readonly class PaymentDTO
{
  public function __construct(
    public BillingTypeEnum $payment_method,
    public float $amount,
    public string $order_id,
    public string $name,
    public ?string $card_number = null,
    public ?string $expiration = null,
    public ?string $cvv = null,
    public ?string $holder = null,
    public ?string $brand = null,
  ) {}

  public static function fromRequest(PaymentRequest $request, int $reservationId, string $guestName, float $total): self
  {
    $validated = $request->validated();

    return new self(
      payment_method: BillingTypeEnum::from($validated['payment_method']),
      amount: $total,
      order_id: 'RES-' . $reservationId,
      name: $guestName ?: 'Cliente',
      card_number: $validated['card_number'] ?? null,
      expiration: $validated['expiration'] ?? null,
      cvv: $validated['cvv'] ?? null,
      holder: $validated['holder'] ?? null,
      brand: $validated['brand'] ?? null,
    );
  }

  public function toArray(): array
  {
    return [
      'order_id'     => $this->order_id,
      'name'         => $this->name,
      'amount'       => $this->amount,
      'payment_method'       => $this->payment_method->value,
      'card_number'  => $this->card_number,
      'expiration'   => $this->expiration,
      'cvv'          => $this->cvv,
      'holder'       => $this->holder,
      'brand'        => $this->brand,
    ];
  }
}
