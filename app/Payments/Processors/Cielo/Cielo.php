<?php

namespace App\Payments\Processors\Cielo;

use App\Payments\Contracts\PaymentProcessorInterface;
use App\Payments\Enums\BillingTypeEnum;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\Request\CieloRequestException;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Merchant;
use Illuminate\Support\Facades\Log;

class Cielo implements PaymentProcessorInterface
{
  protected CieloEcommerce $cielo;

  public function __construct()
  {
    $merchantId  = config('gateway.cielo.merchant_id');
    $merchantKey = config('gateway.cielo.merchant_key');
    $environment = Environment::sandbox();
    $merchant = new Merchant($merchantId, $merchantKey);

    $this->cielo = new CieloEcommerce($merchant, $environment);
  }

  public function createCustomer(array $data): mixed
  {
    return [
      'name'     => $data['name'] ?? 'Cliente',
      'identity' => $data['cpf'] ?? null,
    ];
  }

  public function processPayment(array $data): mixed
  {
    try {
      $billingType = BillingTypeEnum::from($data['method']);

      if ($billingType === BillingTypeEnum::PIX) {
        return app(PixCielo::class)->processPayment($data);
      }

      $amount = (int)($data['amount'] * 100);
      $sale = new Sale($data['order_id'] ?? uniqid());
      $sale->customer($data['name'] ?? 'Cliente');

      $payment = $sale->payment($amount);
      $payment->setCapture(true); // <- força captura automática
      $payment->setSoftDescriptor('Hotel Reserva');

      match ($billingType) {
        BillingTypeEnum::CREDIT_CARD => $this->configureCreditCard($payment, $data),
        BillingTypeEnum::DEBIT_CARD  => $this->configureDebitCard($payment, $data)
      };

      $sale = $this->cielo->createSale($sale);
      $payment = $sale->getPayment();

      return $this->extractResponse($payment);

    } catch (CieloRequestException $e) {
      Log::error('Cielo payment failed', ['error' => $e->getCieloError()]);
      return ['error' => $e->getCieloError()];
    } catch (\Throwable $e) {
      Log::error('Internal payment error', ['error' => $e->getMessage()]);
      return ['error' => $e->getMessage()];
    }
  }

  public function processRefund(string $paymentId, float $amount): mixed
  {
    try {
      return $this->cielo->cancelSale($paymentId, (int)($amount * 100));
    } catch (CieloRequestException $e) {
      Log::error('Cielo refund failed', ['error' => $e->getCieloError()]);
      return ['error' => $e->getCieloError()];
    } catch (\Throwable $e) {
      Log::error('Internal refund error', ['error' => $e->getMessage()]);
      return ['error' => $e->getMessage()];
    }
  }

  protected function configureCreditCard(Payment $payment, array $data): void
  {
    $payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
      ->setInstallments(1)
      ->creditCard($data['cvv'], $data['brand'])
      ->setExpirationDate($data['expiration'])
      ->setCardNumber($data['card_number'])
      ->setHolder($data['holder']);
  }

  protected function configureDebitCard(Payment $payment, array $data): void
  {
    $payment->setType(Payment::PAYMENTTYPE_DEBITCARD)
      ->setAuthenticate(true)
      ->debitCard($data['cvv'], $data['brand'])
      ->setExpirationDate($data['expiration'])
      ->setCardNumber($data['card_number'])
      ->setHolder($data['holder']);
  }

  protected function extractResponse(Payment $payment): array
  {
    return [
      'payment_id'         => $payment->getPaymentId(),
      'return_code'        => $payment->getReturnCode(),
      'return_message'     => $payment->getReturnMessage(),
      'status'             => $payment->getStatus(),
      'tid'                => $payment->getTid(),
      'authorization_code' => $payment->getAuthorizationCode(),
    ];
  }
}
