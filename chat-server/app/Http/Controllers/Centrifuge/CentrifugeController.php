<?php

namespace App\Http\Controllers\Centrifuge;

use App\Http\Controllers\Controller;
use App\Http\Requests\CentrifugoTokenRequest;
use Illuminate\Http\JsonResponse;
use Firebase\JWT\JWT;
use phpcent\Client as CentrifugoClient;

class CentrifugeController extends Controller
{
    private $centrifugo;

    public function __construct()
    {
        $this->centrifugo = new CentrifugoClient(env('WS_API_ENDPOINT'), env('WS_API_KEY'));
        $this->centrifugo->setSecret(env('WS_API_SECRET'));
    }

    /**
     * @param CentrifugoTokenRequest $request
     * @return JsonResponse
     */
    public function getToken(CentrifugoTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        $payload = [
            'sub' => (string) $user->id,
            'exp' => time() + 3600
        ];

        $token = JWT::encode($payload, env('WS_API_SECRET'), 'HS256');

        return response()->json([
            'ws_token' => $token
        ]);
    }
}
