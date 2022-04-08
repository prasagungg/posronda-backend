<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\Like\{LikeCommands, LikeQueries};
use Exception;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $likeCommands, $likeQueries;

    public function __construct()
    {
        $this->middleware('auth:api');

        $this->likeCommands = new LikeCommands();
        $this->likeQueries = new LikeQueries();
    }

    public function getLikeByPost($post_id, Request $request)
    {
        try {
            $limit = is_numeric($request->limit) ? filter_var($request->limit, FILTER_VALIDATE_INT) : 10;

            $post_likes = $this->likeQueries->getLikeByPost($post_id, $limit);

            return response()->successWithData($post_likes);
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }

    public function like($post_id)
    {
        try {
            $like = $this->likeCommands->like($post_id);

            return response()->successWithMessage("The post has been liked", 201);
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }

    public function unlike($post_id)
    {
        try {
            $unlike = $this->likeCommands->unlike($post_id);

            return response()->successWithMessage("The post has been unliked");
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }
}
