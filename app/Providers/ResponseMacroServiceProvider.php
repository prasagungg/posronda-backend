<?php

namespace App\Providers;

use App\StatusCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('token', function ($token, $message = 'Login Successfull') {
            $response = [
                'success' => true,
                'message' => $message,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => (int) Auth::factory()->getTTL(),
                'user' => Auth::user(),
            ];

            return Response::json($response);
        });

        Response::macro('unauthorized', function () {
            $response = [
                'success' => false,
                'message' => 'Unauthorized',
            ];

            return Response::json($response, StatusCode::UNAUTHORIZED);
        });

        Response::macro('withData', function ($data, $success = true, $status = StatusCode::OK, $headers = []) {
            $response = [
                'success' => $success,
                'data' => $data,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('withMessage', function ($message, $success = true, $status = StatusCode::OK, $headers = []) {
            $response = [
                'success' => $success,
                'message' => $message,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('withKey', function ($data, $key = 'data', $success = true, $status = StatusCode::OK, $headers = []) {
            $response = [
                'success' => $success,
                "$key" => $data,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('error', function ($message, $status = StatusCode::BAD_REQUEST, $headers = []) {
            $response = [
                'success' => false,
                'message' => $message,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('dataNotFound', function ($message, $status = StatusCode::NOT_FOUND, $headers = []) {
            $response = [
                'success' => false,
                'message' => $message
            ];

            return Response::json($response, $status, $headers);
        });
    }
}
