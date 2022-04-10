<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\User\UserCommands;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userCommands;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);

        $this->userCommands = new UserCommands();
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
        return response()->withData(Auth::user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->withMessage("User successfully signed out");
    }

    // Registration
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'name' => 'required',
                'username' => 'required|unique:users,username|min:8',
                'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
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

            $user = $this->userCommands->create($request);

            if ($user == false) {
                return response()->withMessage("Register failed.", false);
            }

            return response()->withData($user);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }
}
