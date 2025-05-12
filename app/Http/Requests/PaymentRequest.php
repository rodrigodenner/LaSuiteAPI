<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Payments\Enums\BillingTypeEnum;

class PaymentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $method = $this->input('method');

    $commonRules = [
      'payment_method' => 'required|in:' . implode(',', array_column(BillingTypeEnum::cases(), 'value')),
    ];

    if ($method === BillingTypeEnum::CREDIT_CARD->value || $method === BillingTypeEnum::DEBIT_CARD->value) {
      return array_merge($commonRules, [
        'card_number' => 'required|string|digits_between:13,19',
        'expiration'  => 'required|string|regex:/^(0[1-9]|1[0-2])\/\d{4}$/', // MM/YYYY
        'cvv'         => 'required|string|regex:/^\d{3,4}$/',
        'holder'      => 'required|string|min:3|max:50',
        'brand'       => 'required|string|in:Visa,Mastercard,Amex,Elo,Hipercard',
      ]);
    }

    return $commonRules;
  }

  public function attributes(): array
  {
    return [
      'payment_method'      => 'método de pagamento',
      'card_number' => 'número do cartão',
      'expiration'  => 'data de expiração',
      'cvv'         => 'código de segurança',
      'holder'      => 'nome do titular',
      'brand'       => 'bandeira do cartão',
    ];
  }

  public function messages(): array
  {
    return [
      'payment_method.required'      => 'O método de pagamento é obrigatório.',
      'payment_method.in'            => 'O método de pagamento informado é inválido.',

      'card_number.required' => 'O número do cartão é obrigatório.',
      'card_number.digits_between' => 'O número do cartão deve ter entre 13 e 19 dígitos.',

      'expiration.required'  => 'A data de expiração é obrigatória.',
      'expiration.regex'     => 'A data de expiração deve estar no formato MM/YYYY.',

      'cvv.required'         => 'O código de segurança é obrigatório.',
      'cvv.regex'            => 'O código de segurança deve ter 3 ou 4 dígitos.',

      'holder.required'      => 'O nome do titular é obrigatório.',
      'holder.min'           => 'O nome do titular deve ter no mínimo :min caracteres.',
      'holder.max'           => 'O nome do titular deve ter no máximo :max caracteres.',

      'brand.required'       => 'A bandeira do cartão é obrigatória.',
      'brand.in'             => 'A bandeira do cartão informada é inválida.',
    ];
  }
}
