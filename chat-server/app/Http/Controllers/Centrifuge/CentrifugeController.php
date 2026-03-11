<?php

namespace App\Http\Controllers\Centrifuge;

use App\Http\Controllers\Controller;
use App\Http\Requests\CentrifugoTokenRequest;
use Illuminate\Http\JsonResponse;

class CentrifugeController extends Controller
{

    public function __construct()
    {
        // TODO: Create a CentrifugoClient instance and set the API key and secret
    }

    /**
     * @param CentrifugoTokenRequest $request
     * @return JsonResponse
     */
    public function getToken(CentrifugoTokenRequest $request): JsonResponse
    {
        // TODO: Generate a connection token for the authenticated user and return it as a JSON response
        // Note that the token should be valid for a certain period of time (e.g., 1 hour)
        // The client-side expects the token to be returned in a JSON object with the key "ws_token"
        return response()->json();
    }
}
