<?php

namespace App\Payments\Contracts;

use App\Payments\Enums\BillingTypeEnum;

interface PaymentInterface extends PurchasablePaymentInterface
{
  public function withProcessor(string | PaymentProcessorInterface $processor): self;
  public function withMethod(BillingTypeEnum $method): self;
  public function charge(array $data): mixed;
  public function refund(string $paymentId, float $amount): mixed;
  public function createCustomer(array $data): mixed;
}
