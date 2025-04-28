<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Regime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegimeController extends Controller
{
  /**
   * @OA\Get(
   *     path="/regimes",
   *     tags={"Regimes"},
   *     summary="List all regimes",
   *     description="Retrieve a list of all hotel regimes.",
   *     operationId="getAllRegimes",
   *     @OA\Response(
   *         response=200,
   *         description="List of regimes retrieved successfully.",
   *         @OA\JsonContent(
   *             type="object",
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="List of regimes retrieved successfully."),
   *             @OA\Property(
   *                 property="data",
   *                 type="array",
   *                 @OA\Items(
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="description", type="string", example="All Inclusive"),
   *                     @OA\Property(property="is_active", type="integer", enum={0,1}, example=1),
   *                     @OA\Property(property="created_at", type="string", example="2025-04-25T10:00:00Z"),
   *                     @OA\Property(property="updated_at", type="string", example="2025-04-25T10:00:00Z")
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="No regimes found.",
   *         @OA\JsonContent(
   *             type="object",
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="No regimes found."),
   *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
   *         )
   *     )
   * )
   */

  public function index(Regime $regime)
  {
    $regimes = $regime->all();
    if ($regimes->isEmpty()) {
      return response()->json([
        'success' => false,
        'message' => 'Nenhum regime encontrado.',
        'data' => [],
      ], Response::HTTP_OK);
    }
    return response()->json([
      'success' => true,
      'message' => 'Lista de regimes recuperada com sucesso.',
      'data' => $regimes,
    ], Response::HTTP_OK);
  }

  /**
   * @OA\Get(
   *     path="/regimes/{id}",
   *     tags={"Regimes"},
   *     summary="Get a regime by ID",
   *     description="Retrieve a specific hotel regime by its ID.",
   *     operationId="getRegimeById",
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         required=true,
   *         description="ID of the regime to retrieve",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Regime retrieved successfully."
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Regime not found."
   *     )
   * )
   */
  public function show($id, Regime $regime)
  {
    $regime = $regime->find($id);
    if (!$regime) {
      return response()->json([
        'success' => false,
        'message' => 'Regime não encontrado.',
        'data' => [],
      ], Response::HTTP_NOT_FOUND);
    }
    return response()->json([
      'success' => true,
      'message' => 'Regime recuperado com sucesso.',
      'data' => $regime,
    ], Response::HTTP_OK);
  }

  /**
   * @OA\Post(
   *     path="/regimes",
   *     tags={"Regimes"},
   *     summary="Create a new regime",
   *     description="Create a new hotel regime.",
   *     operationId="createRegime",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"description"},
   *             @OA\Property(property="description", type="string", example="All Inclusive"),
   *             @OA\Property(property="is_active", type="boolean", example=true, default=true)
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Regime created successfully."
   *     ),
   *     @OA\Response(
   *         response=400,
   *         description="Invalid input."
   *     )
   * )
   */
  public function store(Request $request, Regime $regime)
  {
    $request->validate([
      'description' => 'required|string|max:50',
      'is_active' => 'sometimes|boolean',
    ]);

    try {
      $regime = $regime->create($request->only(['description', 'is_active']));
      return response()->json([
        'success' => true,
        'message' => 'Regime criado com sucesso.',
        'data' => $regime,
      ], Response::HTTP_CREATED);
    } catch (\Exception $exception) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao criar regime: ' . $exception->getMessage(),
        'data' => [],
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * @OA\Put(
   *     path="/regimes/{id}",
   *     tags={"Regimes"},
   *     summary="Update a regime",
   *     description="Update a specific hotel regime by its ID. Only the fields sent in the request will be updated.",
   *     operationId="updateRegime",
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         required=true,
   *         description="The ID of the regime to be updated",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             @OA\Property(
   *                 property="description",
   *                 type="string",
   *                 maxLength=50,
   *                 example="All Inclusive",
   *                 description="The description of the regime"
   *             ),
   *             @OA\Property(
   *                 property="is_active",
   *                 type="integer",
   *                 enum={0, 1},
   *                 example=1,
   *                 description="Specifies whether the regime is active (1) or inactive (0)."
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Regime updated successfully."
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Regime not found."
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error."
   *     )
   * )
   */
  public function update(Request $request, Regime $regime, $id)
  {
    $regime = $regime->find($id);
    if (!$regime) {
      return response()->json([
        'success' => false,
        'message' => 'Regime não encontrado.',
        'data' => [],
      ], Response::HTTP_NOT_FOUND);
    }

    $request->validate([
      'description' => 'sometimes|string|max:50',
      'is_active' => 'sometimes|boolean',
    ]);

    try {
      $regime->update($request->only(['description', 'is_active']));
      return response()->json([
        'success' => true,
        'message' => 'Regime atualizado com sucesso.',
        'data' => $regime,
      ], Response::HTTP_OK);
    } catch (\Exception $exception) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao atualizar regime: ' . $exception->getMessage(),
        'data' => [],
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * @OA\Delete(
   *     path="/regimes/{id}",
   *     tags={"Regimes"},
   *     summary="Delete a regime",
   *     description="Delete a specific hotel regime by its ID.",
   *     operationId="deleteRegime",
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         required=true,
   *         description="The ID of the regime to be deleted",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Regime deleted successfully.",
   *         @OA\JsonContent(
   *             type="object",
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Regime deleted successfully.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Regime not found.",
   *         @OA\JsonContent(
   *             type="object",
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Regime not found.")
   *         )
   *     )
   * )
   */
  public function destroy(Request $request, Regime $regime, $id)
  {
    $regime = $regime->find($id);
    if(!$regime){
      return response()->json([
        'success' => false,
        'message' => 'Regime não encontrado.',
      ], Response::HTTP_NOT_FOUND);
    }

    $regime->delete();
    return response()->json([
      'success' => true,
      'message' => 'Regime excluído com sucesso.',
    ], Response::HTTP_OK);
  }

}
