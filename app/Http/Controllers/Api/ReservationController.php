<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateGuestDTO;
use App\DTOs\UpdateReservationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationFilterRequest;
use App\Http\Requests\ReservationStoreRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\GuestWithReservationsResource;
use App\Http\Resources\ReservationWithGuestResource;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\Reservations\ReservationCreateService;
use App\Services\Reservations\ReservationFilterService;
use App\Services\Reservations\ReservationUpdateService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
  public function __construct(
    protected ReservationCreateService $reservationCreateService,
    protected ReservationFilterService $reservationFilterService,
    protected ReservationUpdateService $reservationUpdateService
  ) {}

  /**
   * @OA\Get(
   *     path="/reservations",
   *     summary="List guests with optional filters",
   *     description="Returns a paginated list of guests and their reservations. You can filter by name, CPF, check-in date range, reservation type, room ID, and reservation status.",
   *     operationId="getGuests",
   *     tags={"Reservations"},
   *     security={{ "bearerAuth":{} }},
   *
   *     @OA\Parameter(
   *         name="name",
   *         in="query",
   *         required=false,
   *         description="Filter by guest name (partial match)",
   *         @OA\Schema(type="string", example="Ana")
   *     ),
   *     @OA\Parameter(
   *         name="cpf",
   *         in="query",
   *         required=false,
   *         description="Filter by CPF (exact match)",
   *         @OA\Schema(type="string", example="18473592011")
   *     ),
   *     @OA\Parameter(
   *         name="is_foreigner",
   *         in="query",
   *         required=false,
   *         description="Filter by whether the guest is a foreigner (0 or 1)",
   *         @OA\Schema(type="boolean", example=false)
   *     ),
   *     @OA\Parameter(
   *         name="checkin_from",
   *         in="query",
   *         required=false,
   *         description="Filter check-in starting from this date (YYYY-MM-DD)",
   *         @OA\Schema(type="string", format="date", example="2025-07-01")
   *     ),
   *     @OA\Parameter(
   *         name="checkin_to",
   *         in="query",
   *         required=false,
   *         description="Filter check-in up to this date (YYYY-MM-DD)",
   *         @OA\Schema(type="string", format="date", example="2025-07-31")
   *     ),
   *     @OA\Parameter(
   *         name="type",
   *         in="query",
   *         required=false,
   *         description="Reservation type",
   *         @OA\Schema(type="string", enum={"primary", "dependent", "child", "external"}, example="dependent")
   *     ),
   *     @OA\Parameter(
   *         name="room_id",
   *         in="query",
   *         required=false,
   *         description="Filter by room ID",
   *         @OA\Schema(type="integer", example=5)
   *     ),
   *     @OA\Parameter(
   *         name="reservation_status",
   *         in="query",
   *         required=false,
   *         description="Filter by reservation status",
   *         @OA\Schema(type="string", enum={"pending", "confirmed", "canceled"}, example="pending")
   *     ),
   *
   *     @OA\Response(
   *         response=200,
   *         description="Guests fetched successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(
   *                 property="data",
   *                 type="array",
   *                 @OA\Items(
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="name", type="string", example="Maria Oliveira"),
   *                     @OA\Property(property="birthday", type="string", example="1985-03-22"),
   *                     @OA\Property(property="cpf", type="string", example="62145832987"),
   *                     @OA\Property(property="rg", type="string", example="25874196"),
   *                     @OA\Property(property="passport", type="string", example=null),
   *                     @OA\Property(property="is_foreigner", type="boolean", example=false),
   *                     @OA\Property(property="created_at", type="string", example="2025-05-02T18:38:30+00:00"),
   *                     @OA\Property(
   *                         property="reservations",
   *                         type="array",
   *                         @OA\Items(
   *                             @OA\Property(property="id", type="integer", example=1),
   *                             @OA\Property(property="check_in", type="string", example="2025-08-01"),
   *                             @OA\Property(property="check_out", type="string", example="2025-08-07"),
   *                             @OA\Property(property="adults", type="integer", example=1),
   *                             @OA\Property(property="children", type="integer", example=0),
   *                             @OA\Property(property="created_at", type="string", example="2025-05-02T18:38:30+00:00"),
   *                             @OA\Property(
   *                                 property="rooms",
   *                                 type="array",
   *                                 @OA\Items(
   *                                     @OA\Property(property="id", type="integer", example=6),
   *                                     @OA\Property(property="name", type="string", example="Quarto 364"),
   *                                     @OA\Property(property="slug", type="string", example="quarto-364"),
   *                                     @OA\Property(property="description", type="string", example="Room description here."),
   *                                     @OA\Property(property="size", type="string", example="39m²"),
   *                                     @OA\Property(property="max_adults", type="integer", example=2),
   *                                     @OA\Property(property="max_children", type="integer", example=0),
   *                                     @OA\Property(property="double_beds", type="integer", example=1),
   *                                     @OA\Property(property="single_beds", type="integer", example=0),
   *                                     @OA\Property(property="floor", type="string", example="1"),
   *                                     @OA\Property(property="type", type="string", example="Deluxe"),
   *                                     @OA\Property(property="number", type="string", example="779"),
   *                                     @OA\Property(property="price_per_day", type="string", example="216.00"),
   *                                     @OA\Property(property="regime", type="string", example="Café da Manhã"),
   *                                     @OA\Property(property="days", type="integer", example=6),
   *                                     @OA\Property(property="subtotal", type="number", example=1296)
   *                                 )
   *                             ),
   *                             @OA\Property(property="total", type="string", example="2406.00"),
   *                             @OA\Property(
   *                                 property="statuses",
   *                                 type="array",
   *                                 @OA\Items(
   *                                     @OA\Property(property="status", type="string", example="pending"),
   *                                     @OA\Property(property="created_at", type="string", example="2025-05-02 18:38")
   *                                 )
   *                             )
   *                         )
   *                     )
   *                 )
   *             )
   *         )
   *     ),
   *
   *     @OA\Response(
   *         response=500,
   *         description="Internal server error",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Erro ao buscar hóspedes")
   *         )
   *     )
   * )
   */
  public function index(ReservationFilterRequest $request)
  {
    try {
      $guests = $this->reservationFilterService->filter($request);

      return response()->json([
        'success' => true,
        'data' => GuestWithReservationsResource::collection($guests),
      ]);
    } catch (\Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao buscar hóspedes',
      ], 500);
    }
  }

  /**
   * @OA\Post(
   *     path="/reservations",
   *     summary="Create a new guest with reservation",
   *     tags={"Reservations"},
   *     operationId="storeGuest",
   *     security={{ "bearerAuth": {} }},
   *
   *     @OA\RequestBody(
   *         required=true,
   *         description="Guest data with reservation and contact information",
   *         @OA\JsonContent(
   *             required={"guest", "phones", "addresses", "reservation"},
   *             @OA\Property(
   *                 property="guest",
   *                 type="object",
   *                 required={"name", "birthday", "is_foreigner"},
   *                 @OA\Property(property="name", type="string", example="João da Silva"),
   *                 @OA\Property(property="birthday", type="string", format="date", example="1990-05-10"),
   *                 @OA\Property(property="cpf", type="string", example="12345678900", nullable=true),
   *                 @OA\Property(property="rg", type="string", example="12345678", nullable=true),
   *                 @OA\Property(property="passport", type="string", example="XZ123456", nullable=true),
   *                 @OA\Property(property="is_foreigner", type="boolean", example=false)
   *             ),
   *             @OA\Property(
   *                 property="phones",
   *                 type="array",
   *                 @OA\Items(
   *                     type="object",
   *                     required={"phone_number", "type"},
   *                     @OA\Property(property="phone_number", type="string", example="11999998888"),
   *                     @OA\Property(property="type", type="string", enum={"mobile", "home", "work"}, example="mobile")
   *                 )
   *             ),
   *             @OA\Property(
   *                 property="addresses",
   *                 type="array",
   *                 @OA\Items(
   *                     type="object",
   *                     required={"zipcode", "state", "city", "district", "street", "number", "country"},
   *                     @OA\Property(property="zipcode", type="string", example="12345-678"),
   *                     @OA\Property(property="state", type="string", example="SP"),
   *                     @OA\Property(property="city", type="string", example="São Paulo"),
   *                     @OA\Property(property="district", type="string", example="Centro"),
   *                     @OA\Property(property="street", type="string", example="Rua das Flores"),
   *                     @OA\Property(property="number", type="string", example="100"),
   *                     @OA\Property(property="complement", type="string", example="Apto 202", nullable=true),
   *                     @OA\Property(property="country", type="string", example="Brasil")
   *                 )
   *             ),
   *             @OA\Property(
   *                 property="reservation",
   *                 type="object",
   *                 required={"checkin_at", "checkout_at", "adults", "room_id", "type", "regime_id"},
   *                 @OA\Property(property="checkin_at", type="string", format="date", example="2025-05-10"),
   *                 @OA\Property(property="checkout_at", type="string", format="date", example="2025-05-15"),
   *                 @OA\Property(property="adults", type="integer", example=2),
   *                 @OA\Property(property="children", type="integer", example=1),
   *                 @OA\Property(property="room_id", type="integer", example=1),
   *                 @OA\Property(property="type", type="string", enum={"primary", "dependent", "child", "external"}, example="primary"),
   *                 @OA\Property(property="regime_id", type="integer", example=1)
   *             )
   *         )
   *     ),
   *
   *     @OA\Response(
   *         response=201,
   *         description="Guest successfully created",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Hóspede criado com sucesso."),
   *             @OA\Property(
   *                 property="data",
   *                 type="object",
   *                 @OA\Property(property="id", type="integer", example=1),
   *                 @OA\Property(property="name", type="string", example="João da Silva"),
   *                 @OA\Property(property="birthday", type="string", example="1990-05-10"),
   *                 @OA\Property(property="cpf", type="string", example="12345678900"),
   *                 @OA\Property(property="rg", type="string", example="12345678"),
   *                 @OA\Property(property="passport", type="string", example=null),
   *                 @OA\Property(property="is_foreigner", type="boolean", example=false),
   *                 @OA\Property(property="address", type="object",
   *                     @OA\Property(property="zipcode", type="string", example="12345-678"),
   *                     @OA\Property(property="state", type="string", example="SP"),
   *                     @OA\Property(property="city", type="string", example="São Paulo"),
   *                     @OA\Property(property="district", type="string", example="Centro"),
   *                     @OA\Property(property="street", type="string", example="Rua das Flores"),
   *                     @OA\Property(property="number", type="string", example="100"),
   *                     @OA\Property(property="complement", type="string", example="Apto 202"),
   *                     @OA\Property(property="country", type="string", example="Brasil")
   *                 ),
   *                 @OA\Property(property="phones", type="array",
   *                     @OA\Items(
   *                         type="object",
   *                         @OA\Property(property="phone_number", type="string", example="11999998888"),
   *                         @OA\Property(property="type", type="string", example="mobile")
   *                     )
   *                 ),
   *                 @OA\Property(property="latest_reservation", type="object",
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="checkin_at", type="string", example="2025-05-10 00:00:00"),
   *                     @OA\Property(property="checkout_at", type="string", example="2025-05-15 00:00:00"),
   *                     @OA\Property(property="adults", type="integer", example=2),
   *                     @OA\Property(property="children", type="integer", example=1),
   *                     @OA\Property(property="total", type="number", format="float", example=1200.00),
   *                     @OA\Property(property="status", type="string", example="pending"),
   *                     @OA\Property(property="room", type="object",
   *                         @OA\Property(property="id", type="integer", example=1),
   *                         @OA\Property(property="name", type="string", example="Quarto 854"),
   *                         @OA\Property(property="slug", type="string", example="quarto-854"),
   *                         @OA\Property(property="featured", type="boolean", example=true),
   *                         @OA\Property(property="description", type="string", example="Descrição do quarto"),
   *                         @OA\Property(property="size", type="string", example="57m²"),
   *                         @OA\Property(property="max_adults", type="integer", example=2),
   *                         @OA\Property(property="max_children", type="integer", example=1),
   *                         @OA\Property(property="double_beds", type="integer", example=1),
   *                         @OA\Property(property="single_beds", type="integer", example=2),
   *                         @OA\Property(property="floor", type="string", example="5"),
   *                         @OA\Property(property="type", type="string", example="Deluxe"),
   *                         @OA\Property(property="number", type="string", example="265"),
   *                         @OA\Property(property="regime", type="object",
   *                             @OA\Property(property="id", type="integer", example=1),
   *                             @OA\Property(property="name", type="string", example="Café da Manhã"),
   *                             @OA\Property(property="description", type="string", example="Inclui café da manhã")
   *                         )
   *                     )
   *                 ),
   *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-30 12:15:41")
   *             )
   *         )
   *     ),
   *
   *     @OA\Response(
   *         response=422,
   *         description="Validation failed",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="The given data was invalid."),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     ),
   *
   *     @OA\Response(
   *         response=409,
   *         description="Duplicate reservation period for the same guest",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Já existe uma reserva para este hóspede neste mesmo período.")
   *         )
   *     )
   * )
   */
  public function store(ReservationStoreRequest $request)
  {
    try {
      $dto = CreateGuestDTO::makeFromRequest($request);
      $guest = $this->reservationCreateService->execute($dto);

      return response()->json([
        'success' => true,
        'message' => 'Hóspede criado com sucesso.',
        'data'    => new ReservationResource($guest),
      ], Response::HTTP_CREATED);

    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], Response::HTTP_CONFLICT);
    }
  }

  /**
   * @OA\Get(
   *     path="/reservations/{reservationId}",
   *     summary="Get reservation details",
   *     tags={"Reservations"},
   *     operationId="showReservation",
   *     security={{ "bearerAuth": {} }},
   *     @OA\Parameter(
   *         name="reservationId",
   *         in="path",
   *         required=true,
   *         description="ID da reserva",
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Reservation details returned successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="id", type="integer", example=2),
   *                 @OA\Property(property="check_in", type="string", example="2025-12-15"),
   *                 @OA\Property(property="check_out", type="string", example="2025-12-20"),
   *                 @OA\Property(property="adults", type="integer", example=3),
   *                 @OA\Property(property="children", type="integer", example=2),
   *                 @OA\Property(property="total", type="string", example="3095.00"),
   *                 @OA\Property(property="rooms", type="array",
   *                     @OA\Items(type="object",
   *                         @OA\Property(property="id", type="integer", example=5),
   *                         @OA\Property(property="name", type="string", example="Quarto 303"),
   *                         @OA\Property(property="slug", type="string", example="quarto-303"),
   *                         @OA\Property(property="description", type="string", example="Temporibus magni labore sit iusto accusamus."),
   *                         @OA\Property(property="size", type="string", example="34m²"),
   *                         @OA\Property(property="max_adults", type="integer", example=4),
   *                         @OA\Property(property="max_children", type="integer", example=1),
   *                         @OA\Property(property="double_beds", type="integer", example=0),
   *                         @OA\Property(property="single_beds", type="integer", example=3),
   *                         @OA\Property(property="floor", type="string", example="7"),
   *                         @OA\Property(property="type", type="string", example="Deluxe"),
   *                         @OA\Property(property="number", type="string", example="491"),
   *                         @OA\Property(property="price_per_day", type="string", example="228.00"),
   *                         @OA\Property(property="regime", type="string", example="Café da Manhã"),
   *                         @OA\Property(property="days", type="integer", example=5),
   *                         @OA\Property(property="subtotal", type="number", example=1140)
   *                     )
   *                 ),
   *                 @OA\Property(property="statuses", type="array",
   *                     @OA\Items(type="object",
   *                         @OA\Property(property="status", type="string", example="pending"),
   *                         @OA\Property(property="created_at", type="string", example="2025-05-02 18:38")
   *                     )
   *                 ),
   *                 @OA\Property(property="guest", type="object",
   *                     @OA\Property(property="id", type="integer", example=2),
   *                     @OA\Property(property="name", type="string", example="Carlos Eduardo"),
   *                     @OA\Property(property="birthday", type="string", nullable=true, example=null),
   *                     @OA\Property(property="cpf", type="string", example="74315928640"),
   *                     @OA\Property(property="rg", type="string", example="36985217"),
   *                     @OA\Property(property="passport", type="string", nullable=true, example=null),
   *                     @OA\Property(property="is_foreigner", type="boolean", example=false),
   *                     @OA\Property(property="created_at", type="string", example="2025-05-02T18:38:40+00:00")
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Reservation not found",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Reserva não encontrada.")
   *         )
   *     )
   * )
   */

  public function show($reservationId)
  {
    $reservation = Reservation::with([
      'guests',
      'rooms.tariffs.regime',
      'statuses',
    ])->find($reservationId);

    if (!$reservation) {
      return response()->json([
        'success' => false,
        'message' => 'Reserva não encontrada.',
      ], Response::HTTP_NOT_FOUND);
    }

    return response()->json([
      'success' => true,
      'data' => new ReservationWithGuestResource($reservation),
    ]);
  }

  /**
   * @OA\Put(
   *     path="/reservations/{reservationId}",
   *     summary="Update a reservation",
   *     tags={"Reservations"},
   *     operationId="updateReservation",
   *     security={{ "bearerAuth": {} }},
   *     @OA\Parameter(
   *         name="reservationId",
   *         in="path",
   *         required=true,
   *         description="ID da reserva",
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         description="Reservation data to update",
   *         @OA\JsonContent(
   *             required={"check_in", "check_out", "adults", "room_id"},
   *             @OA\Property(property="check_in", type="string", format="date", example="2025-08-01"),
   *             @OA\Property(property="check_out", type="string", format="date", example="2025-08-07"),
   *             @OA\Property(property="adults", type="integer", example=2),
   *             @OA\Property(property="children", type="integer", example=0),
   *             @OA\Property(property="room_id", type="integer", example=1),
   *             @OA\Property(property="type", type="string", enum={"primary", "dependent"}, example="primary"),
   *             @OA\Property(property="regime_id", type="integer", example=1)
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Reservation updated successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Reserva atualizada com sucesso.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Reservation not found",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Reserva não encontrada.")
   *         )
   *     )
   * )
   */

  public function update(UpdateReservationRequest $request, $reservationId)
  {
    $dto = UpdateReservationDTO::makeFromRequest($request);
    return $this->reservationUpdateService->execute($dto, $reservationId);
  }

  /**
   * @OA\Delete(
   *     path="/reservations/{reservationId}",
   *     summary="Delete a reservation",
   *     tags={"Reservations"},
   *     operationId="deleteReservation",
   *     security={{ "bearerAuth": {} }},
   *     @OA\Parameter(
   *         name="reservationId",
   *         in="path",
   *         required=true,
   *         description="ID da reserva",
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Reservation deleted successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Reserva excluída com sucesso.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Reservation not found",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Reserva não encontrada.")
   *         )
   *     )
   * )
   */
  public function destroy($reservationId)
  {
    $reservation = Reservation::find($reservationId);

    if (!$reservation) {
      return response()->json([
        'success' => false,
        'message' => 'Reserva não encontrada.',
      ], Response::HTTP_NOT_FOUND);
    }

    $reservation->delete();

    return response()->json([
      'success' => true,
      'message' => 'Reserva excluída com sucesso.',
    ], Response::HTTP_OK);
  }

}
