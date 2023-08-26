<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\SecurityChecker\Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(LoginUserRequest $request){
        try {
            if (Checker::isParamsFoundInRequest()){
                return Checker::CheckerResponse();
            }
            // validate data coming by request
            $request->validated($request->all());

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $userAuth = auth()->user();


            return response()->json([
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'user' => UserResource::make($userAuth),
            ], 200);

        }catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
