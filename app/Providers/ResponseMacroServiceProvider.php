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
        Response::macro('token', function ($token) {
            $response = [
                'success' => true,
                'message' => 'Login Successfull',
                'token' => $token,
                'token_type' => 'bearer',
                'expired_in' => (int) Auth::guard('api')->factory()->getTTL(),
            ];

            return Response::json($response);
        });

        Response::macro('success', function ($data, $status = StatusCode::OK, $headers = []) {
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
