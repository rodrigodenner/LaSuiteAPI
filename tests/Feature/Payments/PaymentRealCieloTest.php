<?php

namespace Feature\Payments;

use App\Payments\Enums\BillingTypeEnum;
use App\Payments\Payment;
use App\Payments\Processors\Cielo\Cielo;
use Tests\TestCase;

class PaymentRealCieloTest extends TestCase
{
  public function test_real_credit_card_payment_with_cielo()
  {
    $payment = (new Payment())
      ->withProcessor(Cielo::class)
      ->withMethod(BillingTypeEnum::CREDIT_CARD);

    $response = $payment->charge([
      'order_id'     => uniqid('order_'),
      'name'         => 'Cliente Teste',
      'amount'       => 1.00,
      'method'       => BillingTypeEnum::CREDIT_CARD->value,
      'card_number'  => '4024007153763191', // Cartão de teste (autorizado)
      'holder'       => 'JOSE CLIENTE',
      'expiration'   => '12/2030',
      'cvv'          => '123',
      'brand'        => 'Visa',
    ]);

    // Verificações
    $this->assertArrayHasKey('payment_id', $response);
    $this->assertContains((string)$response['status'], ['2', '4', '6']);
    $this->assertEquals('Operation Successful', $response['return_message'] ?? null);

  }
}
