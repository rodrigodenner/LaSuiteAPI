<?php

namespace App\Payments;

use App\Payments\Contracts\PaymentInterface;
use App\Payments\Contracts\PaymentProcessorInterface;
use App\Payments\Enums\BillingTypeEnum;

class Payment implements PaymentInterface
{
  protected ?PaymentProcessorInterface $processor = null;
  protected ?BillingTypeEnum $paymentMethod = null;

  public function withProcessor(string|PaymentProcessorInterface $processor): PaymentInterface
  {
    if (is_string($processor)) {
      $this->processor = app($processor);
    } else {
      $this->processor = $processor;
    }
    return $this;
  }

  public function withMethod(BillingTypeEnum $method): PaymentInterface
  {
    $this->paymentMethod = $method;
    return $this;
  }

  public function createCustomer(array $data): mixed
  {
    $this->ensureProcessorIsSet();
    return $this->processor->createCustomer($data);
  }

  public function charge(array $data): mixed
  {
    $this->ensureProcessorIsSet();
    return $this->processor->processPayment($data);
  }

  public function refund(string $paymentId, float $amount): mixed
  {
    $this->ensureProcessorIsSet();
    return $this->processor->processRefund($paymentId, $amount);
  }

  protected function ensureProcessorIsSet(): void
  {
    if (!$this->processor) {
      throw new \RuntimeException('Payment processor has not been set. Use withProcessor() method.');
    }
  }

  public function getPurchaseDescription(): string
  {
    // Implementação para obter a descrição do item de compra única
    // Isso dependerá da entidade pagável que está sendo processada
    throw new \RuntimeException('Method getPurchaseDescription() must be implemented by the purchasable entity.');
  }

  public function getPurchaseItemId(): string
  {
    // Implementação para obter o ID do item de compra única
    // Isso dependerá da entidade pagável que está sendo processada
    throw new \RuntimeException('Method getPurchaseItemId() must be implemented by the purchasable entity.');
  }

  public function getPurchaseAmount(): float
  {
    // Implementação para obter o valor total da compra única
    // Isso dependerá da entidade pagável que está sendo processada
    throw new \RuntimeException('Method getPurchaseAmount() must be implemented by the purchasable entity.');
  }
}
