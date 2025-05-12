<?php

namespace App\Services\Payment;

use App\DTOs\PaymentDTO;
use App\Models\Reservation;
use App\Payments\Contracts\PaymentInterface;
use Symfony\Component\HttpFoundation\Response;

class Billing
{
  public function __construct(protected PaymentInterface $payment) {}

  public function execute(PaymentDTO $dto, Reservation $reservation): array
  {
    try {
      $result = $this->payment
        ->withMethod($dto->payment_method)
        ->charge($dto->toArray());

      $payment = $reservation->payments()->create([
        'total'              => $reservation->total,
        'current_status'     => 'pending',
        'payment_method'     => $dto->payment_method->value,
        'payment_id'         => $result['payment_id'] ?? null,
        'pix_payload'        => $result['qrcode_payload'] ?? null,
        'pix_qrcode'         => $result['qrcode_base64'] ?? null,
        'authorization_code' => $result['authorization_code'] ?? null,
        'tid'                => $result['tid'] ?? null,
        'details'            => $result,
      ]);

      $payment->statuses()->create(['status' => 'pending']);

      return [
        'success' => true,
        'data'    => $result,
        'status_code' => Response::HTTP_OK
      ];

    } catch (\ValueError $e) {
      return [
        'success' => false,
        'message' => 'Método de pagamento inválido.',
        'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY
      ];
    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => 'Erro ao processar pagamento.',
        'error'   => $e->getMessage(),
        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
      ];
    }
  }
}
