<?php

namespace App\Http\Requests;

use App\Models\GuestReservation;
use Illuminate\Foundation\Http\FormRequest;

class GuestStoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      // Dados do hóspede agrupados em "guest"
      'guest.name'         => 'required|string|min:3|max:50',
      'guest.birthday'     => 'required|date|before:today',
      'guest.is_foreigner' => 'required|boolean',
      'guest.cpf'          => 'required_if:guest.is_foreigner,0|string|max:14',
      'guest.rg'           => 'required_if:guest.is_foreigner,0|string|max:15',
      'guest.passport'     => 'required_if:guest.is_foreigner,1|string|max:20',

      // Telefones
      'phones' => 'required|array',
      'phones.*.phone_number' => 'required|string|max:20',
      'phones.*.type'   => 'required|string|in:mobile,home,work',


      // Endereço
      'addresses' => 'required|array',
      'addresses.*.zipcode'    => 'required|string|max:20',
      'addresses.*.state'      => 'required|string|max:100',
      'addresses.*.city'       => 'required|string|max:100',
      'addresses.*.district'   => 'required|string|max:100',
      'addresses.*.street'     => 'required|string|max:255',
      'addresses.*.number'     => 'required|string|max:20',
      'addresses.*.complement' => 'nullable|string|max:255',
      'addresses.*.country'    => 'required|string|max:100',

      // Reserva
      'reservation.checkin_at'  => 'required|date|after_or_equal:today',
      'reservation.checkout_at' => 'required|date|after:reservation.checkin',
      'reservation.adults'   => 'required|integer|min:1',
      'reservation.children' => 'nullable|integer|min:0',
      'reservation.room_id'  => 'required|exists:rooms,id',
      'reservation.type' => 'required|string|in:' . implode(',', GuestReservation::types()),


    ];
  }

  public function attributes(): array
  {
    return [
      'guest.name'         => 'nome',
      'guest.birthday'     => 'data de nascimento',
      'guest.is_foreigner' => 'estrangeiro',
      'guest.cpf'          => 'CPF',
      'guest.rg'           => 'RG',
      'guest.passport'     => 'passaporte',

      'phones' => 'telefones',
      'phones.*.phone_number' => 'número do telefone',
      'phones.*.type'   => 'tipo do telefone',


      'addresses.*.zipcode'    => 'CEP',
      'addresses.*.state'      => 'estado',
      'addresses.*.city'       => 'cidade',
      'addresses.*.district'   => 'bairro',
      'addresses.*.street'     => 'rua',
      'addresses.*.number'     => 'número',
      'addresses.*.complement' => 'complemento',
      'addresses.*.country'    => 'país',

      'reservation.checkin_at'  => 'data de entrada',
      'reservation.checkout_at' => 'data de saída',
      'reservation.adults'   => 'adultos',
      'reservation.children' => 'crianças',
      'reservation.room_id'  => 'quarto',
      'reservation.type' => 'tipo de hóspede',



    ];
  }

  public function messages(): array
  {
    return [
      '*.required'    => 'O campo :attribute é obrigatório.',
      '*.string'      => 'O campo :attribute deve ser um texto.',
      '*.integer'     => 'O campo :attribute deve ser um número inteiro.',
      '*.date'        => 'O campo :attribute deve ser uma data válida.',
      '*.boolean'     => 'O campo :attribute deve ser verdadeiro ou falso.',
      '*.in'          => 'O campo :attribute tem um valor inválido.',
      '*.max'         => 'O campo :attribute deve ter no máximo :max caracteres.',
      '*.min'         => 'O campo :attribute deve ter no mínimo :min.',
      '*.exists'      => 'O :attribute selecionado é inválido.',
      '*.required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    ];
  }
}
