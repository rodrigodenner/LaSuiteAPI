<?php

namespace Feature\Payments;

use App\Payments\Enums\BillingTypeEnum;
use App\Payments\Payment;
use App\Payments\Processors\Cielo\Cielo;
use Tests\TestCase;

class PaymentRefusedCieloTest extends TestCase
{
  public function test_card_declined_status_05()
  {
    $response = $this->makePayment('4024007153763192'); // final 2 = recusado

    $this->assertEquals('05', $response['return_code']);
    $this->assertContains((string) $response['status'], ['2', '3']);
  }

  public function test_card_expired_status_57()
  {
    $response = $this->makePayment('4024007153763193'); // final 3 = expirado

    $this->assertEquals('57', $response['return_code']);
    $this->assertContains((string) $response['status'], ['2', '3']);
  }

  public function test_card_blocked_status_78()
  {
    $response = $this->makePayment('4024007153763195'); // final 5 = bloqueado

    $this->assertEquals('78', $response['return_code']);
    $this->assertContains((string) $response['status'], ['2', '3']);
  }

  public function test_card_cancelled_status_77()
  {
    $response = $this->makePayment('4024007153763197'); // final 7 = cancelado

    $this->assertEquals('77', $response['return_code']);
    $this->assertContains((string) $response['status'], ['2', '3']);
  }

  private function makePayment(string $cardNumber): array
  {
    $payment = (new Payment())
      ->withProcessor(Cielo::class)
      ->withMethod(BillingTypeEnum::CREDIT_CARD);

    return $payment->charge([
      'order_id'     => uniqid('order_'),
      'name'         => 'Cliente Teste',
      'amount'       => 1.00,
      'method'       => BillingTypeEnum::CREDIT_CARD->value,
      'card_number'  => $cardNumber,
      'holder'       => 'JOSE TESTE',
      'expiration'   => '12/2030',
      'cvv'          => '123',
      'brand'        => 'Visa',
    ]);
  }
}
