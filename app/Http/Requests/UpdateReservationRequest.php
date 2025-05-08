<?php

namespace App\Http\Requests;

use App\Models\GuestReservation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
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
          // Reserva
          'reservation.checkin_at'  => 'sometimes|date|after_or_equal:today',
          'reservation.checkout_at' => 'sometimes|date|after:reservation.checkin',
          'reservation.adults'   => 'sometimes|integer|min:1',
          'reservation.children' => 'nullable|integer|min:0',
          'reservation.room_id'  => 'sometimes|exists:rooms,id',
          'reservation.type' => 'sometimes|string|in:' . implode(',', GuestReservation::types()),
          'reservation.regime_id' => 'sometimes|exists:regimes,id',


        ];
    }

  public function attributes(): array
  {
    return [
      'reservation.checkin_at'  => 'data de check-in',
      'reservation.checkout_at' => 'data de check-out',
      'reservation.adults'      => 'adultos',
      'reservation.children'    => 'crianças',
      'reservation.room_id'     => 'quarto',
      'reservation.type'        => 'tipo',
      'reservation.regime_id'   => 'regime',
    ];
  }

  public function messages()
  {
    return [
      'reservation.checkin_at.date'  => 'O campo :attribute deve ser uma data válida.',
      'reservation.checkout_at.date' => 'O campo :attribute deve ser uma data válida.',
      'reservation.adults.integer'   => 'O campo :attribute deve ser um número inteiro.',
      'reservation.children.integer' => 'O campo :attribute deve ser um número inteiro.',
      'reservation.room_id.exists'   => 'O campo :attribute deve existir na tabela de quartos.',
      'reservation.type.in'          => 'O campo :attribute deve ser um dos seguintes valores: :values.',
      'reservation.regime_id.exists' => 'O campo :attribute deve existir na tabela de regimes.',
    ];
  }
}
