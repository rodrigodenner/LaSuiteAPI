<?php

namespace App\Http\Controllers\Api;

use App\DTOs\AvailableRoomsDTO;
use App\DTOs\CreateRoomDTO;
use App\DTOs\UpdateRoomDTO;
use App\DTOs\UpdateTariffDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableRoomsListRequest;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Requests\UpdateTariffsRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Services\Rooms\AvailableRoomsService;
use App\Services\Rooms\RoomCreationService;
use App\Services\Rooms\RoomUpdateService;
use App\Services\Rooms\TariffUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Rooms",
 *     description="Endpoints for managing hotel rooms"
 * )
 */
class RoomController extends Controller
{
  public function __construct(
    protected AvailableRoomsService $availableRoomsService,
    protected TariffUpdateService $tariffUpdateService,
  )
  {
  }

  /**
   * @OA\Get(
   *     path="/rooms",
   *     tags={"Rooms"},
   *     summary="List all rooms (no filters)",
   *     description="Retrieves all registered rooms with pagination (10 items per page).",
   *     security={{ "bearerAuth":{} }},
   *     @OA\Response(
   *         response=200,
   *         description="Room list retrieved successfully.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Room list retrieved successfully."),
   *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Room"))
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated"
   *     )
   * )
   */
  public function index(Room $room)
  {
    $rooms = $room->with(['images', 'tariffs.regime', 'availabilities'])->paginate(10);

    if ($rooms->isEmpty()) {
      return response()->json([
        'success' => false,
        'message' => 'Nenhum quarto encontrado.',
        'data' => [],
      ], Response::HTTP_OK);
    }

    return response()->json([
      'success' => true,
      'message' => 'Lista de quartos recuperada com sucesso.',
      'data' => RoomResource::collection($rooms),
    ], Response::HTTP_OK);
  }

  /**
   * @OA\Get(
   *     path="/rooms/available",
   *     tags={"Rooms"},
   *     summary="List available rooms with filters",
   *     description="Filtra e lista quartos disponíveis. Regras:
   *       - 'checkin' é obrigatório e deve ser hoje ou uma data futura.
   *       - 'checkout' é obrigatório e deve ser após o 'checkin'.
   *       - 'adults' opcional, mínimo 1 se informado.
   *       - 'children' opcional, mínimo 0 se informado.
   *       - 'price_min' e 'price_max' opcionais, valores numéricos, mínimo 0.
   *       - 'sort' aceita: price_asc, price_desc, title_asc, title_desc.",
   *     security={{ "bearerAuth":{} }},
   *     @OA\Parameter(
   *         name="checkin",
   *         in="query",
   *         required=true,
   *         description="Data de check-in (obrigatória, formato YYYY-MM-DD, deve ser hoje ou no futuro).",
   *         @OA\Schema(type="string", format="date")
   *     ),
   *     @OA\Parameter(
   *         name="checkout",
   *         in="query",
   *         required=true,
   *         description="Data de check-out (obrigatória, formato YYYY-MM-DD, deve ser depois do check-in).",
   *         @OA\Schema(type="string", format="date")
   *     ),
   *     @OA\Parameter(
   *         name="adults",
   *         in="query",
   *         required=false,
   *         description="Número de adultos (opcional, mínimo 1 se informado).",
   *         @OA\Schema(type="integer", minimum=1)
   *     ),
   *     @OA\Parameter(
   *         name="children",
   *         in="query",
   *         required=false,
   *         description="Número de crianças (opcional, mínimo 0).",
   *         @OA\Schema(type="integer", minimum=0)
   *     ),
   *     @OA\Parameter(
   *         name="price_min",
   *         in="query",
   *         required=false,
   *         description="Preço mínimo (opcional, numérico, mínimo 0).",
   *         @OA\Schema(type="number", format="float", minimum=0)
   *     ),
   *     @OA\Parameter(
   *         name="price_max",
   *         in="query",
   *         required=false,
   *         description="Preço máximo (opcional, numérico, mínimo 0).",
   *         @OA\Schema(type="number", format="float", minimum=0)
   *     ),
   *     @OA\Parameter(
   *         name="sort",
   *         in="query",
   *         required=false,
   *         description="Método de ordenação (opcional): 'price_asc', 'price_desc', 'title_asc', 'title_desc'.",
   *         @OA\Schema(type="string", enum={"price_asc", "price_desc", "title_asc", "title_desc"})
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Quartos disponíveis listados com sucesso.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Lista de quartos disponíveis recuperada com sucesso."),
   *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Room"))
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Erro de validação nos parâmetros.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Erro de validação."),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Não autenticado (Bearer Token ausente ou inválido)."
   *     )
   * )
   */
  public function available(AvailableRoomsListRequest $request)
  {
    try {
      $dto = AvailableRoomsDTO::makeFromRequest($request);
      $rooms = $this->availableRoomsService->execute($dto);

      return response()->json([
        'success' => true,
        'message' => 'Lista de quartos recuperada com sucesso.',
        'data' => RoomResource::collection($rooms),
      ], Response::HTTP_OK);
    } catch (Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Ocorreu um erro inesperado ao processar a sua solicitação. Por favor, tente novamente mais tarde.',
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * @OA\Get(
   *     path="/rooms/{slug}",
   *     tags={"Rooms"},
   *     summary="Get room details",
   *     description="Retrieve the details of a specific room by its slug.",
   *     security={{ "bearerAuth":{} }},
   *     @OA\Parameter(
   *         name="slug",
   *         in="path",
   *         required=true,
   *         description="Slug of the room",
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Room details retrieved successfully.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Detalhes do quarto recuperados com sucesso."),
   *             @OA\Property(
   *                 property="data",
   *                 type="object",
   *                 @OA\Property(property="id", type="integer", example=1),
   *                 @OA\Property(property="name", type="string", example="Quarto 101"),
   *                 @OA\Property(property="description", type="string", example="Quarto confortável com vista."),
   *                 @OA\Property(property="slug", type="string", example="quarto-101-de-teste"),
   *                 @OA\Property(property="featured", type="boolean", example=true),
   *                 @OA\Property(property="size", type="string", example="40m²"),
   *                 @OA\Property(property="max_adults", type="integer", example=2),
   *                 @OA\Property(property="max_children", type="integer", example=1),
   *                 @OA\Property(property="max_capacity", type="integer", example=3),
   *                 @OA\Property(property="double_beds", type="integer", example=1),
   *                 @OA\Property(property="single_beds", type="integer", example=2),
   *                 @OA\Property(property="floor", type="string", example="3"),
   *                 @OA\Property(property="type", type="string", example="Suíte"),
   *                 @OA\Property(property="number", type="string", example="101"),
   *                 @OA\Property(
   *                     property="images",
   *                     type="array",
   *                     @OA\Items(
   *                         @OA\Property(property="path", type="string", example="storage/rooms/quarto-101-de-teste/image1.jpg"),
   *                         @OA\Property(property="description", type="string", example="Vista da sacada"),
   *                         @OA\Property(property="alt", type="string", example="Foto da sacada do quarto"),
   *                         @OA\Property(property="featured", type="boolean", example=true)
   *                     )
   *                 ),
   *                 @OA\Property(
   *                     property="tariffs",
   *                     type="array",
   *                     @OA\Items(
   *                         @OA\Property(property="regime", type="string", example="Café da Manhã"),
   *                         @OA\Property(property="start_date", type="string", example="2025-05-01"),
   *                         @OA\Property(property="end_date", type="string", example="2025-05-10"),
   *                         @OA\Property(property="type", type="string", example="daily"),
   *                         @OA\Property(property="value_room", type="number", format="float", example=300),
   *                         @OA\Property(property="additional_adult", type="number", format="float", example=50),
   *                         @OA\Property(property="additional_child", type="number", format="float", example=30)
   *                     )
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Room not found."
   *     )
   * )
   */
  public function show(string $slug)
  {
    try {
      $room = Room::with(['images', 'tariffs.regime', 'availabilities'])
        ->where('slug', $slug)
        ->firstOrFail();

      return response()->json([
        'success' => true,
        'message' => 'Detalhes do quarto recuperados com sucesso.',
        'data' => new RoomResource($room),
      ], Response::HTTP_OK);

    } catch (ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Quarto não encontrado.',
      ], Response::HTTP_NOT_FOUND);

    } catch (Throwable $e) {
      Log::error('Erro ao buscar detalhes do quarto: ' . $e->getMessage(), ['slug' => $slug]);
      return response()->json([
        'success' => false,
        'message' => 'Ocorreu um erro inesperado ao processar a sua solicitação.',
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * @OA\Post(
   *     path="/rooms",
   *     tags={"Rooms"},
   *     summary="Create a new room",
   *     description="Creates a new room including tariffs, images (only one can be marked as 'featured'), and availabilities. Images must be uploaded via 'multipart/form-data'.",
   *     operationId="createRoom",
   *     security={{ "bearerAuth":{} }},
   *     @OA\RequestBody(
   *         required=true,
   *         description="Room creation data",
   *         @OA\JsonContent(ref="#/components/schemas/RoomRequest")
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Room successfully created.",
   *         @OA\JsonContent(ref="#/components/schemas/Room")
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="The provided data is invalid."),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Unexpected server error."
   *     )
   * )
   */
  public function store(RoomStoreRequest $request, RoomCreationService $roomCreationService)
  {
    try {
      $dto = CreateRoomDTO::makeFromRequest($request);
      $room = $roomCreationService->create($dto);

      return response()->json([
        'success' => true,
        'message' => 'Quarto criado com sucesso.',
        'data' => new RoomResource($room),
      ], Response::HTTP_CREATED);
    } catch (Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro inesperado ao criar o quarto. Tente novamente mais tarde.',
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * @OA\Put(
   *     path="/rooms/{id}",
   *     tags={"Rooms"},
   *     summary="Update an existing room",
   *     description="Updates an existing room. You can send only the fields you want to update. Tariffs, images, and availabilities will be replaced if provided. Images must be uploaded via 'multipart/form-data'.",
   *     operationId="updateRoom",
   *     security={{ "bearerAuth":{} }},
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         description="ID of the room to be updated",
   *         required=true,
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         description="Room update data (only send the fields you want to change)",
   *         @OA\JsonContent(ref="#/components/schemas/RoomRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Room successfully updated.",
   *         @OA\JsonContent(ref="#/components/schemas/Room")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Room not found.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Quarto não encontrado.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="The provided data is invalid."),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Unexpected server error."
   *     )
   * )
   */
  public function update(UpdateRoomRequest $request, string|int $id, RoomUpdateService $roomUpdateService)
  {
    try {
      $room = Room::findOrFail($id);
      $dto = UpdateRoomDTO::makeFromRequest($request);
      $room = $roomUpdateService->update($room, $dto);

      return response()->json([
        'success' => true,
        'message' => 'Quarto atualizado com sucesso.',
        'data' => new RoomResource($room),
      ]);
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Quarto não encontrado.',
      ], Response::HTTP_NOT_FOUND);
    } catch (\Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro inesperado ao atualizar o quarto. Tente novamente mais tarde.',
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }


  /**
   * @OA\Put(
   *     path="/rooms/{id}/tariffs",
   *     tags={"Rooms"},
   *     summary="Update room tariffs",
   *     description="Updates the tariffs of a room for different regimes. Only value-related fields are updated.",
   *     security={{ "bearerAuth": {} }},
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         required=true,
   *         description="ID of the room",
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"tariffs"},
   *             @OA\Property(
   *                 property="tariffs",
   *                 type="array",
   *                 @OA\Items(
   *                     type="object",
   *                     required={"regime_id", "value_room", "additional_adult", "additional_child"},
   *                     @OA\Property(property="regime_id", type="integer", example=1),
   *                     @OA\Property(property="value_room", type="number", format="float", example=350),
   *                     @OA\Property(property="additional_adult", type="number", format="float", example=60),
   *                     @OA\Property(property="additional_child", type="number", format="float", example=40)
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Tariffs updated successfully.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Tariffs updated successfully.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="No tariffs provided.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="No tariffs were provided for update.")
   *         )
   *     )
   * )
   */
  public function updateTariffs(UpdateTariffsRequest $request, int $roomId)
  {
    $tariffs = $request->validated()['tariffs'] ?? [];

    if (empty($tariffs)) {
      return response()->json([
        'success' => false,
        'message' => 'Nenhuma tarifa foi enviada para atualização.',
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $dtos = collect($tariffs)
      ->map(fn($tariff) => UpdateTariffDTO::fromArray($tariff))
      ->toArray();

    $this->tariffUpdateService->execute($roomId, $dtos);

    return response()->json([
      'success' => true,
      'message' => 'Tarifas atualizadas com sucesso.',
    ]);
  }

  /**
   * @OA\Delete(
   *     path="/rooms/{id}",
   *     tags={"Rooms"},
   *     summary="Delete a room",
   *     description="Deletes an existing room by ID. If the room does not exist, a 404 error is returned.",
   *     operationId="deleteRoom",
   *     security={{ "bearerAuth":{} }},
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         description="ID of the room to be deleted",
   *         required=true,
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Room successfully deleted.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Quarto excluído com sucesso.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Room not found.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Quarto não encontrado.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Unexpected server error.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Erro inesperado ao deletar o quarto. Tente novamente mais tarde.")
   *         )
   *     )
   * )
   */
  public function destroy(string|int $id)
  {
    try {
      $room = Room::findOrFail($id);
      $room->delete();

      return response()->json([
        'success' => true,
        'message' => 'Quarto excluído com sucesso.',
      ], Response::HTTP_OK);

    } catch (ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Quarto não encontrado.',
      ], Response::HTTP_NOT_FOUND);
    } catch (Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro inesperado ao deletar o quarto. Tente novamente mais tarde.',
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }


}
