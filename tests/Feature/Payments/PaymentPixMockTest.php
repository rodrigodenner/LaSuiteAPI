<?php

namespace Tests\Feature\Payments;

use Tests\TestCase;
use App\Payments\Payment;
use App\Payments\Enums\BillingTypeEnum;
use App\Payments\Contracts\PaymentProcessorInterface;

class PaymentPixMockTest extends TestCase
{
  public function test_pix_payment_with_mocked_processor()
  {

    $mock = $this->createMock(PaymentProcessorInterface::class);

    $mock->expects($this->once())
      ->method('processPayment')
      ->with($this->arrayHasKey('amount'))
      ->willReturn([
        'payment_id' => 'pix_test_123',
        'qrcode_payload' => '00020126360014BR.GOV.BCB.PIX...',
        'status' => '12',
      ]);

    $payment = (new Payment())
      ->withProcessor($mock)
      ->withMethod(BillingTypeEnum::PIX);

    $response = $payment->charge([
      'order_id' => uniqid('order_'),
      'name'     => 'Cliente Pix',
      'amount'   => 1.00,
      'method'   => BillingTypeEnum::PIX->value,
    ]);

    $this->assertEquals('pix_test_123', $response['payment_id']);
    $this->assertEquals('12', $response['status']);
    $this->assertStringContainsString('BR.GOV.BCB.PIX', $response['qrcode_payload']);
  }
}
