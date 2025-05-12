<?php

namespace App\Services\Reservations;

use App\DTOs\UpdateReservationDTO;
use App\Http\Resources\ReservationWithGuestResource;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\Calculators\ReservationTotalCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class ReservationUpdateService
{
  public function __construct(
    protected ReservationTotalCalculatorService $calculatorService
  ) {}

  public function execute(UpdateReservationDTO $dto, int $reservationId): JsonResponse
  {
    $reservation = $this->getReservation($reservationId);
    if (!$reservation) {
      return $this->notFoundResponse();
    }

    $reservation->checkin_at  = $dto->checkin_at ?? $reservation->checkin_at;
    $reservation->checkout_at = $dto->checkout_at ?? $reservation->checkout_at;
    $reservation->adults      = $dto->adults ?? $reservation->adults;
    $reservation->children    = $dto->children ?? $reservation->children;

    if ($dto->room_id || $dto->regime_id) {
      $tariffResponse = $this->handleTariffUpdate($reservation, $dto);
      if ($tariffResponse instanceof JsonResponse) {
        return $tariffResponse;
      }
    }

    if ($dto->type) {
      $typeResponse = $this->updateType($reservation, $dto->type);
      if ($typeResponse instanceof JsonResponse) {
        return $typeResponse;
      }
    }

    $reservation->save();

    return response()->json([
      'success' => true,
      'message' => 'Reserva atualizada com sucesso.',
      'data' => new ReservationWithGuestResource(
        $reservation->fresh(['guests', 'rooms.tariffs.regime', 'statuses'])
      ),
    ]);
  }


  private function getReservation(int $id): ?Reservation
  {
    return Reservation::with(['guests', 'rooms.tariffs.regime', 'statuses'])->find($id);
  }

  private function notFoundResponse(): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => 'Reserva não encontrada.',
    ], Response::HTTP_NOT_FOUND);
  }



  private function handleTariffUpdate(Reservation $reservation, UpdateReservationDTO $dto): ?JsonResponse
  {
    $roomId = $dto->room_id ?? $reservation->rooms->first()?->id;
    $room = Room::with('tariffs')->find($roomId);

    if (!$room) {
      return $this->errorResponse('Quarto não encontrado ou não possui tarifas.');
    }

    $regimeId = $dto->regime_id ?? $room->tariffs->first()?->regime_id;

    try {
      $checkin  = $reservation->checkin_at;
      $checkout = $reservation->checkout_at;
      $adults   = $reservation->adults;
      $children = $reservation->children;

      $newTotal = $this->calculatorService->calculate(
        $room,
        $checkin,
        $checkout,
        $adults,
        $children,
        $regimeId
      );

      if ($newTotal < $reservation->total) {
        return response()->json([
          'success' => false,
          'message' => 'O novo valor da reserva é inferior ao atual. FAVOR ENTRAR EM CONTATO COM O HOTEL.',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
      }

      if ($dto->room_id) {
        $reservation->rooms()->sync([$dto->room_id]);
      }

      $reservation->total = $newTotal;
    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage());
    }

    return null;
  }


  private function updateType(Reservation $reservation, string $newType): JsonResponse|bool
  {
    $guest = $reservation->guests->first();
    $pivot = $guest?->pivot;

    if ($pivot && $pivot->type === 'primary') {
      $pivot->type = $newType;
      $pivot->save();
      return true;
    }

    return response()->json([
      'success' => false,
      'message' => 'Apenas reservas do tipo "primary" podem alterar o tipo.',
    ], Response::HTTP_FORBIDDEN);
  }

  private function errorResponse(string $message): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => $message,
    ], Response::HTTP_UNPROCESSABLE_ENTITY);
  }
}
