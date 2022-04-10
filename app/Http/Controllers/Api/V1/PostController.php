<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\Post\{PostCommands, PostQueries};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $postCommands, $postQueries;

    public function __construct()
    {
        $this->middleware('auth:api');

        $this->postCommands = new PostCommands();
        $this->postQueries = new PostQueries();
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'caption' => 'nullable|string',
                'images' => 'required|array|min:1',
                'images.*.url' => 'required',
                'images.*.tags' => 'array|exists:users,id',
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

            $post = $this->postCommands->create($request);

            return response()->withData($post, true, 201);
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }

    public function update($id, Request $request)
    {
        try {
            //code...
        } catch (Exception $e) {
            return $this->respondErrorException($e, $request);
        }
    }

    public function destroy($id)
    {
        try {
            //code...
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }

    public function all(Request $request)
    {
        try {
            $limit = is_numeric($request->limit) ? filter_var($request->limit, FILTER_VALIDATE_INT) : 10;

            $posts = $this->postQueries->all($limit);

            return response()->withData($posts);
        } catch (Exception $e) {
            return $this->respondErrorException($e, request());
        }
    }
}
