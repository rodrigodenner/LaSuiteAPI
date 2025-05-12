<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTariffsRequest extends FormRequest
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
        'tariffs' => 'required|array|min:1',
        'tariffs.*.regime_id' => 'required|exists:regimes,id',
        'tariffs.*.value_room' => 'required|numeric|min:0',
        'tariffs.*.additional_adult' => 'required|numeric|min:0',
        'tariffs.*.additional_child' => 'required|numeric|min:0',
      ];
    }

  public function attributes()
  {
    return [
      'tariffs' => 'Tarifas',
      'tariffs.*.regime_id' => 'ID do regime',
      'tariffs.*.value_room' => 'Valor do quarto',
      'tariffs.*.additional_adult' => 'Valor adicional adulto',
      'tariffs.*.additional_child' => 'Valor adicional criança',
    ];
  }


  public function messages()
  {
    return [
      'tariffs.required' => 'As tarifas são obrigatórias.',
      'tariffs.array' => 'As tarifas devem ser um array.',
      'tariffs.min' => 'Pelo menos uma tarifa é necessária.',

      'tariffs.*.regime_id.required' => 'O ID do regime é obrigatório.',
      'tariffs.*.regime_id.exists' => 'O ID do regime deve existir.',

      'tariffs.*.value_room.required' => 'O valor do quarto é obrigatório.',
      'tariffs.*.value_room.numeric' => 'O valor do quarto deve ser numérico.',
      'tariffs.*.value_room.min' => 'O valor do quarto deve ser maior ou igual a 0.',

      'tariffs.*.additional_adult.required' => 'O valor adicional para adulto é obrigatório.',
      'tariffs.*.additional_adult.numeric' => 'O valor adicional para adulto deve ser numérico.',
      'tariffs.*.additional_adult.min' => 'O valor adicional para adulto deve ser maior ou igual a 0.',

      'tariffs.*.additional_child.required' => 'O valor adicional para criança é obrigatório.',
      'tariffs.*.additional_child.numeric' => 'O valor adicional para criança deve ser numérico.',
      'tariffs.*.additional_child.min' => 'O valor adicional para criança deve ser maior ou igual a 0.',
    ];
  }

}
