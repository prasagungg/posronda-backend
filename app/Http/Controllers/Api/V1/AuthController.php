<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = collect();

                foreach ($validator->errors()->getMessages() as $key => $value) {
                    foreach ($value as $error) {
                        $errors[$key] = $error;
                    }
                }

                return response()->errorValidation($errors);
            }

            $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            if (!$token = Auth::attempt([$fieldType => $request->username, 'password' => $request->password])) {
                return response()->errorValidation("Incorrect username or password");
            }

            return response()->token($token);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }

    public function refresh()
    {
        return response()->token(Auth::refresh(), 'Refresh token successfull');
    }

    public function userProfile()
    {
        return response()->successWithData(Auth::user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->successWithMessage("User successfully signed out");
    }
}
