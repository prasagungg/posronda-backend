<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\Follower\{FollowerCommands, FollowerQueries};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FollowersController extends Controller
{
    protected $followerQueries, $followerCommands;

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['follow', 'unfollow']]);
        $this->followerCommands = new FollowerCommands();
        $this->followerQueries = new FollowerQueries();
    }

    public function follow($user_id)
    {
        try {
            $follow = $this->followerCommands->follow($user_id);

            return response()->withMessage($follow);
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }

    public function unfollow($user_id)
    {
        try {
            $unfollow = $this->followerCommands->unfollow($user_id);

            return response()->withMessage($unfollow);
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }

    public function getFollowers($user_id, Request $request)
    {
        try {
            $limit = is_numeric($request->limit) ? filter_var($request->limit, FILTER_VALIDATE_INT) : 10;
            $orderby = !empty($request->orderby) ? $request->orderby : 'created_at';
            $sort = !empty($request->sort) ? $request->sort : 'desc';
            $filter = $request->filter ?? [];

            $followers = $this->followerQueries->getFollowers($user_id, $limit, $orderby, $sort, $filter);

            return response()->withData($followers);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }

    public function getFollowings($user_id, Request $request)
    {
        try {
            $limit = is_numeric($request->limit) ? filter_var($request->limit, FILTER_VALIDATE_INT) : 10;
            $orderby = !empty($request->orderby) ? $request->orderby : 'created_at';
            $sort = !empty($request->sort) ? $request->sort : 'desc';
            $filter = $request->filter ?? [];

            $followings = $this->followerQueries->getFollowings($user_id, $limit, $orderby, $sort, $filter);

            return response()->withData($followings);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }
}
