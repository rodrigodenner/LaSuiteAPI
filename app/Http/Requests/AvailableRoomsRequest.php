<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class AvailableRoomsRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'checkin'     => 'required|date|after_or_equal:today',
      'checkout'    => 'required|date|after:checkin',
      'adults'      => 'nullable|integer|min:1',
      'children'    => 'nullable|integer|min:0',
      'price_min'   => 'nullable|numeric|min:0',
      'price_max'   => 'nullable|numeric|min:0',
      'sort'        => 'nullable|in:price_asc,price_desc,title_asc,title_desc',
    ];
  }

  protected function failedValidation(Validator $validator): void
  {
    throw new HttpResponseException(response()->json([
      'message' => 'Erro de validação.',
      'errors'  => $validator->errors(),
    ], Response::HTTP_UNPROCESSABLE_ENTITY));
  }

  public function attributes(): array
  {
    return [
      'checkin'    => 'data de chegada',
      'checkout'   => 'data de saída',
      'adults'     => 'número de adultos',
      'children'   => 'número de crianças',
      'price_min'  => 'preço mínimo',
      'price_max'  => 'preço máximo',
      'sort'       => 'ordenação',
    ];
  }

  public function messages(): array
  {
    return [
      'checkin.required'         => 'A :attribute é obrigatória.',
      'checkin.date'             => 'A :attribute deve ser uma data válida.',
      'checkin.after_or_equal'   => 'A :attribute deve ser hoje ou uma data futura.',
      'checkout.required'        => 'A :attribute é obrigatória.',
      'checkout.date'            => 'A :attribute deve ser uma data válida.',
      'checkout.after'           => 'A :attribute deve ser depois da data de chegada.',
      'adults.integer'           => 'O :attribute deve ser um número inteiro.',
      'adults.min'               => 'O :attribute deve ser pelo menos 1.',
      'children.integer'         => 'O :attribute deve ser um número inteiro.',
      'children.min'             => 'O :attribute não pode ser negativo.',
      'price_min.numeric'        => 'O :attribute deve ser um valor numérico.',
      'price_max.numeric'        => 'O :attribute deve ser um valor numérico.',
      'sort.in'                  => 'O campo :attribute deve ser um dos seguintes valores: price_asc, price_desc, title_asc, title_desc.',
    ];
  }
}
