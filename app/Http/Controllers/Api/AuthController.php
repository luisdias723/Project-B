<?php
namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Extra\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends BaseController
{
protected $maxLoginAttempts = 10; // Amount of bad attempts user can make
protected $lockoutTime = 300; // Time for which user is going to be blocked in seconds

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        Log::debug('Test debug message');

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(new JsonResponse([], 'Email ou password errados'), Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();
        $token = $user->createToken('laravue');

        return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json((new JsonResponse())->success([]), Response::HTTP_OK);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }
}