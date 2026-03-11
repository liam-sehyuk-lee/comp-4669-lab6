<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegistrationController
{

    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = new User;
        $validatedData = $request->validated();
        $user->fill($validatedData);
        $user->password = Hash::make($validatedData['password'], [
            'rounds' => 12,
        ]);
        $user->save();
        $request->session()->regenerate();
        return response()->json($user);
    }
}
