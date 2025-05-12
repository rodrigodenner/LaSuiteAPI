<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'reservation_id'      => $this->reservation_id,
      'total'               => $this->total,
      'status'              => $this->current_status,
      'payment_method'      => $this->payment_method,
      'payment_id'          => $this->payment_id,
      'authorization_code'  => $this->authorization_code,
      'tid'                 => $this->tid,
      'payment_date'        => optional($this->payment_date)->toDateTimeString(),
      'qrcode_payload'      => $this->pix_payload,
      'qrcode_base64'       => $this->pix_qrcode ?: null,
    ];
  }
}
