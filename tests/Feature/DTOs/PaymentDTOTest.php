<?php

namespace Tests\Feature\DTOs;

use Tests\TestCase;
use App\DTOs\PaymentDTO;
use App\Http\Requests\PaymentRequest;
use App\Payments\Enums\BillingTypeEnum;
use Mockery;

class PaymentDTOTest extends TestCase
{
  public function test_can_create_payment_dto_from_request_for_pix()
  {
    $request = Mockery::mock(PaymentRequest::class);
    $request->shouldReceive('validated')
      ->once()
      ->andReturn([
        'payment_method' => BillingTypeEnum::PIX->value,
      ]);

    $dto = PaymentDTO::fromRequest($request, 1, 'Cliente Teste', 100.00);

    $this->assertEquals(BillingTypeEnum::PIX, $dto->payment_method);
    $this->assertEquals('RES-1', $dto->order_id);
    $this->assertEquals('Cliente Teste', $dto->name);
    $this->assertEquals(100.00, $dto->amount);
  }

  public function test_can_create_payment_dto_from_request_for_credit_card()
  {
    $request = Mockery::mock(PaymentRequest::class);
    $request->shouldReceive('validated')
      ->once()
      ->andReturn([
        'payment_method' => BillingTypeEnum::CREDIT_CARD->value,
        'card_number' => '4024007153763191',
        'expiration'  => '12/2030',
        'cvv'         => '123',
        'holder'      => 'JOSE TESTE',
        'brand'       => 'Visa',
      ]);

    $dto = PaymentDTO::fromRequest($request, 2, 'Cliente Cartão', 200.00);

    $this->assertEquals(BillingTypeEnum::CREDIT_CARD, $dto->payment_method);
    $this->assertEquals('RES-2', $dto->order_id);
    $this->assertEquals('Cliente Cartão', $dto->name);
    $this->assertEquals('4024007153763191', $dto->card_number);
    $this->assertEquals('12/2030', $dto->expiration);
    $this->assertEquals('123', $dto->cvv);
    $this->assertEquals('JOSE TESTE', $dto->holder);
    $this->assertEquals('Visa', $dto->brand);
  }

  public function test_to_array_method_returns_correct_structure()
  {
    $dto = new PaymentDTO(
      payment_method: BillingTypeEnum::CREDIT_CARD,
      amount: 300.00,
      order_id: 'RES-3',
      name: 'Teste Final',
      card_number: '4024007153763191',
      expiration: '12/2030',
      cvv: '123',
      holder: 'JOSE TESTE',
      brand: 'Visa'
    );

    $data = $dto->toArray();

    $this->assertEquals('RES-3', $data['order_id']);
    $this->assertEquals('Teste Final', $data['name']);
    $this->assertEquals(300.00, $data['amount']);
    $this->assertEquals(BillingTypeEnum::CREDIT_CARD->value, $data['payment_method']);
    $this->assertEquals('4024007153763191', $data['card_number']);
  }
}
