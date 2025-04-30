<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomStoreRequest extends FormRequest
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
      'name'           => 'required|string|max:100',
      'slug'           => 'required|string|max:30|unique:rooms,slug',
      'description'    => 'required|string',
      'featured'       => 'required|boolean',
      'size'           => 'required|string|max:50',
      'max_adults'     => 'required|integer|min:1',
      'max_children'   => 'required|integer|min:0',
      'double_beds'    => 'required|integer|min:0',
      'single_beds'    => 'required|integer|min:0',
      'floor'          => 'required|string|max:20',
      'type'           => 'required|string|max:50',
      'number'         => 'required|string',

      // Tarifas
      'tariffs'                    => 'required|array|min:1',
      'tariffs.*.regime_id'        => 'required|exists:regimes,id',
      'tariffs.*.start_date'       => 'required|date|before_or_equal:tariffs.*.end_date',
      'tariffs.*.end_date'         => 'required|date|after_or_equal:tariffs.*.start_date',
      'tariffs.*.type'             => 'required|string|in:daily,package',
      'tariffs.*.value_room'       => 'required|numeric|min:0',
      'tariffs.*.additional_adult' => 'required|numeric|min:0',
      'tariffs.*.additional_child' => 'required|numeric|min:0',

      // Imagens
      'images'                     => 'required|array|min:1',
      'images.*.file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
      'images.*.description'       => 'nullable|string|max:255',
      'images.*.alt'               => 'nullable|string|max:255',
      'images.*.featured'          => 'required|boolean',

      // Disponibilidades
      'availabilities' => 'required|array|min:1',
      'availabilities.*.date' => 'required|date',
      'availabilities.*.quantity' => 'required|integer|min:1',


    ];
  }

  public function messages(): array
  {
    return [
      'required'               => 'O campo :attribute é obrigatório.',
      'string'                 => 'O campo :attribute deve ser um texto.',
      'max'                    => 'O campo :attribute não pode ter mais que :max caracteres.',
      'min'                    => 'O campo :attribute deve ser no mínimo :min.',
      'integer'                => 'O campo :attribute deve ser um número inteiro.',
      'numeric'                => 'O campo :attribute deve ser um número.',
      'boolean'                => 'O campo :attribute deve ser verdadeiro ou falso.',
      'exists'                 => 'O :attribute selecionado é inválido.',
      'image'                  => 'O campo :attribute deve ser uma imagem válida.',
      'mimes'                  => 'O campo :attribute deve ser uma imagem do tipo: jpg, jpeg, png, webp.',
      'unique'                 => 'O campo :attribute já está em uso.',
      'before_or_equal'        => 'A data de início deve ser anterior ou igual à data de término.',
      'after_or_equal'         => 'A data de término deve ser posterior ou igual à data de início.',
      'in'                     => 'O valor selecionado para :attribute é inválido.',
      'array'                  => 'O campo :attribute deve ser uma lista/array.',
    ];
  }

  public function attributes(): array
  {
    return [
      'name'                        => 'nome do quarto',
      'slug'                        => 'slug do quarto',
      'description'                 => 'descrição do quarto',
      'featured'                    => 'destaque',
      'size'                        => 'tamanho do quarto',
      'max_adults'                  => 'capacidade máxima de adultos',
      'max_children'                => 'capacidade máxima de crianças',
      'double_beds'                 => 'quantidade de camas de casal',
      'single_beds'                 => 'quantidade de camas de solteiro',
      'floor'                       => 'andar',
      'type'                        => 'tipo de quarto',
      'number'                      => 'número do quarto',
      'tariffs'                     => 'tarifas',
      'tariffs.*.regime_id'         => 'regime da tarifa',
      'tariffs.*.start_date'        => 'data de início da tarifa',
      'tariffs.*.end_date'          => 'data de término da tarifa',
      'tariffs.*.type'              => 'tipo de tarifa',
      'tariffs.*.value_room'        => 'valor da tarifa',
      'tariffs.*.additional_adult'  => 'valor adicional por adulto',
      'tariffs.*.additional_child'  => 'valor adicional por criança',
      'images'                      => 'imagens',
      'images.*.file'               => 'arquivo da imagem',
      'images.*.description'        => 'descrição da imagem',
      'images.*.alt'                => 'texto alternativo da imagem',
      'images.*.featured'           => 'imagem em destaque',
      'availabilities'              => 'disponibilidades',
      'availabilities.*.date'       => 'data de disponibilidade',
      'availabilities.*.quantity'   => 'quantidade disponível',
    ];
  }
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      $featuredCount = collect($this->input('images', []))
        ->where('featured', true)
        ->count();

      if ($featuredCount > 1) {
        $validator->errors()->add('images', 'Apenas uma imagem pode ser marcada como featured.');
      }
    });
  }
}
