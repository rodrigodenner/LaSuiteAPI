<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="hoteltaiyo API",
 *         version="1.0.0",
 *         description="API documentation for the Hotel Taiyo system using Bearer Token authentication."
 *     )
 * )
 */
class AuthController extends Controller
{
  /**
   * @OA\Post(
   *     path="/generate-token",
   *     tags={"Authentication"},
   *     summary="Generate Access Token",
   *     description="Generates a Bearer token for system access using the provided email and password.",
   *     operationId="generateToken",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"email", "password"},
   *             @OA\Property(property="email", type="string", format="email", example="client@hotel.com"),
   *             @OA\Property(property="password", type="string", example="your-secure-password")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Token successfully generated.",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Token generated successfully."),
   *             @OA\Property(
   *                 property="data",
   *                 type="object",
   *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOi..."),
   *                 @OA\Property(property="type", type="string", example="Bearer")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Invalid credentials.",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Invalid credentials.")
   *         )
   *     )
   * )
   */

  public function generateToken(Request $request, User $userModel)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $user = $userModel->where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response()->json([
        'message' => 'Invalid credentials.'
      ], Response::HTTP_UNAUTHORIZED);
    }

    $token = $user->createToken('token-access')->plainTextToken;

    return response()->json([
      'success' => true,
      'message' => 'Token generated successfully.',
      'data' => [
        'token' => $token,
        'type' => 'Bearer',
      ],
    ], Response::HTTP_OK);
  }

}
