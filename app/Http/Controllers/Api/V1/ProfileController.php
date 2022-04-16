<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\Profile\ProfileQueries;
use Exception;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileQueries;

    public function __construct()
    {
        $this->middleware('auth:api');

        $this->profileQueries = new ProfileQueries();
    }

    public function getProfileByUsername($username, Request $request)
    {
        try {
            $user = $this->profileQueries->getProfileByUsername($username);

            return response()->withData($user);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }
}
