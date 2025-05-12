<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
  public function rules(): array
  {
    return [
      'name'               => ['nullable', 'string'],
      'cpf'                => ['nullable', 'string', 'digits_between:11,14'],
      'is_foreigner'       => ['nullable', 'boolean'],
      'checkin_from'       => ['nullable', 'date'],
      'checkin_to'         => ['nullable', 'date'],
      'type'               => ['nullable', 'in:primary,dependent,child,external'],
      'room_id'            => ['nullable', 'integer', 'exists:rooms,id'],
      'reservation_status' => ['nullable', 'string', 'in:pending,confirmed,canceled'],
    ];
  }

  public function attributes()
  {
    return [
      'name'               => 'nome',
      'cpf'                => 'CPF',
      'is_foreigner'       => 'estrangeiro',
      'checkin_from'       => 'data de check-in de',
      'checkin_to'         => 'data de check-in até',
      'type'               => 'tipo',
      'room_id'            => 'quarto',
      'reservation_status' => 'status da reserva',
    ];
  }

  public function messages()
  {
    return [
      'name.string'               => 'O campo :attribute deve ser uma string.',
      'cpf.string'                => 'O campo :attribute deve ser uma string.',
      'cpf.digits_between'        => 'O campo :attribute deve ter entre :min e :max dígitos.',
      'is_foreigner.boolean'      => 'O campo :attribute deve ser verdadeiro ou falso.',
      'checkin_from.date'        => 'O campo :attribute deve ser uma data válida.',
      'checkin_to.date'          => 'O campo :attribute deve ser uma data válida.',
      'type.in'                   => 'O campo :attribute deve ser um dos seguintes valores: :values.',
      'room_id.integer'           => 'O campo :attribute deve ser um número inteiro.',
      'room_id.exists'            => 'O quarto selecionado não existe.',
      'reservation_status.in'     => 'O campo :attribute deve ser um dos seguintes valores: :values.',
    ];
  }
}
