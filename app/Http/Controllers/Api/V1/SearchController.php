<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $limit = is_numeric($request->limit) ? filter_var($request->limit, FILTER_VALIDATE_INT) : 20;
            $search = $request->get('query', '') == '' ? '' : '%' . $request->get('query', '') . '%';

            $users = User::query()
                ->select(['id', 'name', 'username', 'profile_picture', 'is_verified'])
                ->whereLike(['username', 'name'], $search)
                ->orderBy('username', 'ASC')
                ->paginate($limit);

            return response()->withData($users);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }
}
