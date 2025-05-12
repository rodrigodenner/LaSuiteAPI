<?php

namespace App\Payments\Contracts;

interface PurchasablePaymentInterface
{
  public function getPurchaseDescription(): string;
  public function getPurchaseItemId(): string;
  public function getPurchaseAmount(): float;
}
