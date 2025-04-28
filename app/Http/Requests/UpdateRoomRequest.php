<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
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
      'name'           => 'sometimes|string|max:100',
      'slug'           => 'sometimes|string|max:30|unique:rooms,slug,' . $this->route('room'),
      'description'    => 'sometimes|string',
      'featured'       => 'sometimes|boolean',
      'size'           => 'sometimes|string|max:50',
      'max_adults'     => 'sometimes|integer|min:1',
      'max_children'   => 'sometimes|integer|min:0',
      'double_beds'    => 'sometimes|integer|min:0',
      'single_beds'    => 'sometimes|integer|min:0',
      'floor'          => 'sometimes|string|max:20',
      'type'           => 'sometimes|string|max:50',
      'number'         => 'sometimes|string',

      'tariffs'        => 'sometimes|array|min:1',
      'tariffs.*.regime_id'        => 'required_with:tariffs|exists:regimes,id',
      'tariffs.*.start_date'       => 'required_with:tariffs|date|before_or_equal:tariffs.*.end_date',
      'tariffs.*.end_date'         => 'required_with:tariffs|date|after_or_equal:tariffs.*.start_date',
      'tariffs.*.type'             => 'required_with:tariffs|string|in:daily,package',
      'tariffs.*.value_room'       => 'required_with:tariffs|numeric|min:0',
      'tariffs.*.additional_adult' => 'required_with:tariffs|numeric|min:0',
      'tariffs.*.additional_child' => 'required_with:tariffs|numeric|min:0',

      'images'                     => 'sometimes|array',
      'images.*.file'              => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
      'images.*.description'       => 'nullable|string|max:255',
      'images.*.alt'               => 'nullable|string|max:255',
      'images.*.featured'          => 'sometimes|boolean',

      'availabilities'            => 'sometimes|array',
      'availabilities.*.date'     => 'required_with:availabilities|date',
      'availabilities.*.quantity' => 'required_with:availabilities|integer|min:1',
    ];
  }
}
