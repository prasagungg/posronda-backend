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

        Response::macro('successWithData', function ($data, $status = StatusCode::OK, $headers = []) {
            $response = [
                'success' => true,
                'data' => $data,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('successWithMessage', function ($message, $status = StatusCode::OK, $headers = []) {
            $response = [
                'success' => true,
                'message' => $message,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('successWithKey', function ($data, $key = 'data', $status = StatusCode::OK, $headers = []) {
            $response = [
                'success' => true,
                "$key" => $data,
            ];

            return Response::json($response, $status, $headers);
        });

        Response::macro('errorValidation', function ($message, $status = StatusCode::BAD_REQUEST, $headers = []) {
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
