<?php

namespace App\Http\Controllers\Api;

use App\DTOs\PaymentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResponseResource;
use App\Models\Reservation;
use App\Http\Resources\PaymentResource;
use App\Services\Payment\Billing;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
  public function __construct(protected Billing $billing) {}

  /**
   * @OA\Get(
   *     path="/reservations/{reservationId}/payment",
   *     summary="Get latest payment for a reservation",
   *     tags={"Payments"},
   *     security={{"bearerAuth": {}}},
   *     @OA\Parameter(
   *         name="reservationId",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Payment retrieved successfully.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="reservation_id", type="integer", example=1),
   *                 @OA\Property(property="total", type="string", example="1200.00"),
   *                 @OA\Property(property="status", type="string", example="pending"),
   *                 @OA\Property(property="payment_method", type="string", example="pix"),
   *                 @OA\Property(property="qrcode_payload", type="string", example="00020126360014BR.GOV.BCB.PIX..."),
   *                 @OA\Property(property="qrcode_base64", type="string", example="iVBORw0KGgoAAAANSUhEUgAA...")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Reservation not found."
   *     )
   * )
   */
  public function show($reservationId)
  {
    $reservation = Reservation::with('payments.statuses')->find($reservationId);

    if (!$reservation) {
      return response()->json([
        'success' => false,
        'message' => 'Reserva não encontrada.',
      ], Response::HTTP_NOT_FOUND);
    }

    $payment = $reservation->payments()->latest()->first();

    return response()->json([
      'success' => true,
      'data'    => new PaymentResource($payment),
    ]);
  }

  /**
   * @OA\Post(
   *     path="/reservations/{reservationId}/pay",
   *     summary="Process a payment for a reservation",
   *     tags={"Payments"},
   *     security={{"bearerAuth": {}}},
   *     @OA\Parameter(
   *         name="reservationId",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"payment_method"},
   *             @OA\Property(property="payment_method", type="string", enum={"pix", "credit_card", "debit_card"}, example="pix"),
   *             @OA\Property(property="card_number", type="string", example="4024007153763191"),
   *             @OA\Property(property="expiration", type="string", example="12/2030"),
   *             @OA\Property(property="cvv", type="string", example="123"),
   *             @OA\Property(property="holder", type="string", example="JOSE CLIENTE"),
   *             @OA\Property(property="brand", type="string", example="Visa")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Payment processed successfully.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="payment_id", type="string", example="abc123"),
   *                 @OA\Property(property="status", type="string", example="paid"),
   *                 @OA\Property(property="qrcode_payload", type="string", example="00020126360014BR.GOV.BCB.PIX..."),
   *                 @OA\Property(property="qrcode_base64", type="string", example="iVBORw0KGgoAAAANSUhEUgAA...")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Invalid payment method."
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Error processing payment."
   *     )
   * )
   */
  public function pay(PaymentRequest $request, $reservationId)
  {
    $reservation = Reservation::find($reservationId);
    if (!$reservation) {
      return response()->json([
        'success' => false,
        'message' => 'Reserva não encontrada.',
      ], Response::HTTP_NOT_FOUND);
    }

    $guestName = $reservation->guests()->first()?->name ?? 'Cliente';
    $dto = PaymentDTO::fromRequest($request, $reservation->id, $guestName, $reservation->total);
    $result = $this->billing->execute($dto, $reservation);

    return PaymentResponseResource::make($result)
      ->response()
      ->setStatusCode($result['status_code']);
  }
}
