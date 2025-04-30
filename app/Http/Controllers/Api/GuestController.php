<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateGuestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\GuestStoreRequest;
use App\Http\Resources\GuestResource;
use App\Services\Guests\GuestCreateService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{
  public function __construct(
    protected GuestCreateService $guestCreateService
  ) {}

  public function index(Request $request)
  {
    // Listar reservas (com filtro por status, data, etc. se quiser)
  }

  /**
   * @OA\Post(
   *     path="/guests",
   *     summary="Create a new guest with reservation",
   *     tags={"Guests"},
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
   *                 required={"checkin_at", "checkout_at", "adults", "room_id", "type"},
   *                 @OA\Property(property="checkin_at", type="string", format="date", example="2025-05-10"),
   *                 @OA\Property(property="checkout_at", type="string", format="date", example="2025-05-15"),
   *                 @OA\Property(property="adults", type="integer", example=2),
   *                 @OA\Property(property="children", type="integer", example=1),
   *                 @OA\Property(property="room_id", type="integer", example=1),
   *                 @OA\Property(property="type", type="string", enum={"primary", "dependent", "child", "external"}, example="primary")
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
   *                         @OA\Property(property="number", type="string", example="265")
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

  public function store(GuestStoreRequest $request)
  {
    try {
      $dto = CreateGuestDTO::makeFromRequest($request);
      $guest = $this->guestCreateService->execute($dto);

      return response()->json([
        'success' => true,
        'message' => 'Hóspede criado com sucesso.',
        'data'    => new GuestResource($guest),
      ], Response::HTTP_CREATED);

    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], Response::HTTP_CONFLICT);
    }
  }



  public function show($id)
  {
    // Buscar uma reserva pelo ID com guests + rooms + payments + statuses
  }

  public function update(Request $request, $id)
  {
    // Atualizar status, datas ou informações da reserva
  }

  public function destroy($id)
  {
    // Cancelar ou excluir a reserva (soft delete se tiver `SoftDeletes`)
  }
}
