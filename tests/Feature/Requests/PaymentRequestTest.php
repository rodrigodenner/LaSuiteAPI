<?php

namespace Tests\Feature\Requests;

use Tests\TestCase;
use App\Http\Requests\PaymentRequest;
use App\Payments\Enums\BillingTypeEnum;
use Illuminate\Support\Facades\Validator;

class PaymentRequestTest extends TestCase
{
  private function validateRequest(array $data): \Illuminate\Validation\Validator
  {
    $request = new PaymentRequest();
    return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
  }

  public function test_valid_pix_payment_request()
  {
    $validator = $this->validateRequest([
      'payment_method' => BillingTypeEnum::PIX->value,
    ]);

    $this->assertTrue($validator->passes());
  }

  public function test_valid_credit_card_payment_request()
  {
    $validator = $this->validateRequest([
      'payment_method' => BillingTypeEnum::CREDIT_CARD->value,
      'card_number' => '4024007153763191',
      'expiration'  => '12/2030',
      'cvv'         => '123',
      'holder'      => 'JOSE CLIENTE',
      'brand'       => 'Visa',
    ]);

    $this->assertTrue($validator->passes());
  }

  public function test_invalid_credit_card_missing_fields()
  {
    $data = [
      'payment_method' => BillingTypeEnum::CREDIT_CARD->value,
    ];

    $rules = [
      'payment_method' => 'required|in:' . implode(',', array_column(BillingTypeEnum::cases(), 'value')),
      'card_number'    => 'required|string|digits_between:13,19',
      'expiration'     => 'required|string|regex:/^(0[1-9]|1[0-2])\/\d{4}$/',
      'cvv'            => 'required|string|regex:/^\d{3,4}$/',
      'holder'         => 'required|string|min:3|max:50',
      'brand'          => 'required|string|in:Visa,Mastercard,Amex,Elo,Hipercard',
    ];

    $request = new PaymentRequest();
    $validator = Validator::make($data, $rules, $request->messages(), $request->attributes());

    $this->assertFalse($validator->passes());
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('card_number', $validator->errors()->messages());
    $this->assertArrayHasKey('expiration', $validator->errors()->messages());
    $this->assertArrayHasKey('cvv', $validator->errors()->messages());
    $this->assertArrayHasKey('holder', $validator->errors()->messages());
    $this->assertArrayHasKey('brand', $validator->errors()->messages());
  }

  public function test_invalid_payment_method()
  {
    $validator = $this->validateRequest([
      'payment_method' => 'invalid_method',
    ]);

    $this->assertFalse($validator->passes());
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('payment_method', $validator->errors()->messages());
  }
}
