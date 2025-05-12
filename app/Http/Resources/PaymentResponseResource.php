<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResponseResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  Request  $request
   * @return array<string, mixed>
   */
  public function toArray($request): array
  {
    return [
      'success' => $this['success'],
      'data'    => $this['data'] ?? null,
      'message' => $this['message'] ?? null,
      'error'   => $this['error'] ?? null,
    ];
  }
}
