<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Payments\Payment;
use App\Payments\Enums\BillingTypeEnum;
use App\Payments\Contracts\PaymentProcessorInterface;

class PaymentTest extends TestCase
{
  public function test_charge_using_mocked_processor()
  {
    $mockProcessor = $this->createMock(PaymentProcessorInterface::class);
    $mockProcessor->expects($this->once())
      ->method('processPayment')
      ->with(['amount' => 100])
      ->willReturn(['status' => 'success']);


    $payment = (new Payment())
      ->withProcessor($mockProcessor)
      ->withMethod(BillingTypeEnum::CREDIT_CARD);

    $response = $payment->charge(['amount' => 100]);

    $this->assertEquals(['status' => 'success'], $response);
  }

  public function test_refund_using_mocked_processor()
  {
    $mockProcessor = $this->createMock(PaymentProcessorInterface::class);

    $mockProcessor->expects($this->once())
      ->method('processRefund')
      ->with('payment123', 50.0)
      ->willReturn(['status' => 'refunded']);

    $payment = (new Payment())
      ->withProcessor($mockProcessor)
      ->withMethod(BillingTypeEnum::CREDIT_CARD);

    $response = $payment->refund('payment123', 50.0);

    $this->assertEquals(['status' => 'refunded'], $response);
  }

  public function test_create_customer_using_mocked_processor()
  {
    $mockProcessor = $this->createMock(PaymentProcessorInterface::class);

    $customerData = ['name' => 'Rodrigo', 'email' => 'rodrigo@example.com'];

    $mockProcessor->expects($this->once())
      ->method('createCustomer')
      ->with($customerData)
      ->willReturn(['id' => 'cust_abc123']);

    $payment = (new Payment())
      ->withProcessor($mockProcessor)
      ->withMethod(BillingTypeEnum::PIX);

    $response = $payment->createCustomer($customerData);

    $this->assertEquals(['id' => 'cust_abc123'], $response);
  }

  public function test_charge_throws_exception_when_no_processor_is_set()
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Payment processor has not been set');

    $payment = new Payment();
    $payment->charge(['amount' => 100]);
  }

  public function test_refund_throws_exception_when_no_processor_is_set()
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Payment processor has not been set');

    $payment = new Payment();
    $payment->refund('abc', 10.0);
  }

  public function test_charge_returns_error_when_processor_fails()
  {
    $mockProcessor = $this->createMock(PaymentProcessorInterface::class);

    $mockProcessor->expects($this->once())
      ->method('processPayment')
      ->with(['amount' => 100])
      ->willReturn([
        'status' => 'error',
        'message' => 'Card declined',
      ]);

    $payment = (new Payment())
      ->withProcessor($mockProcessor)
      ->withMethod(BillingTypeEnum::CREDIT_CARD);

    $response = $payment->charge(['amount' => 100]);

    $this->assertEquals('error', $response['status']);
    $this->assertEquals('Card declined', $response['message']);
  }

  public function test_refund_returns_error_when_processor_fails()
  {
    $mockProcessor = $this->createMock(PaymentProcessorInterface::class);

    $mockProcessor->expects($this->once())
      ->method('processRefund')
      ->with('invalid_id', 10.0)
      ->willReturn([
        'status' => 'error',
        'message' => 'Invalid payment ID',
      ]);

    $payment = (new Payment())
      ->withProcessor($mockProcessor)
      ->withMethod(BillingTypeEnum::PIX);

    $response = $payment->refund('invalid_id', 10.0);

    $this->assertEquals('error', $response['status']);
    $this->assertEquals('Invalid payment ID', $response['message']);
  }

  public function test_get_purchase_amount_throws_exception()
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Method getPurchaseAmount() must be implemented');

    $payment = new Payment();
    $payment->getPurchaseAmount();
  }

  public function test_get_purchase_description_throws_exception()
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Method getPurchaseDescription() must be implemented');

    $payment = new Payment();
    $payment->getPurchaseDescription();
  }

  public function test_get_purchase_item_id_throws_exception()
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Method getPurchaseItemId() must be implemented');

    $payment = new Payment();
    $payment->getPurchaseItemId();
  }

}
