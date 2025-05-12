<?php

namespace App\Payments\Contracts;

interface PaymentProcessorInterface
{
  public function createCustomer(array $data): mixed;
  public function processPayment(array $data): mixed;
  public function processRefund(string $paymentId, float $amount): mixed;
}
