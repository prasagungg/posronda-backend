<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function () {
    abort(404);
});

Route::get('/', function () {
    return response(['success' => true, 'message' => "Pos Ronda API Service"]);
});

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {
    // Auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('user-profile', [AuthController::class, 'userProfile']);
    });

    // Post
    Route::prefix('post')->group(function () {
        Route::get('', [PostController::class, 'all']);
        Route::post('', [PostController::class, 'create']);
    });

    // Likes
    Route::prefix('likes')->group(function () {
        Route::get('{post_id}', [LikeController::class, 'getLikeByPost'])->where('post_id', '[0-9]+');
        Route::post('{post_id}/like', [LikeController::class, 'like'])->where('post_id', '[0-9]+');
        Route::post('{post_id}/unlike', [LikeController::class, 'unlike'])->where('post_id', '[0-9]+');
    });

    // Comment
    Route::prefix('comments')->group(function () {
        Route::get('{post_id}', [CommentController::class, 'getCommentByPost'])->where('post_id', '[0-9]+');
        Route::post('{post_id}/add', [CommentController::class, 'comment'])->where('post_id', '[0-9]+');
        Route::delete('{post_id}/delete/{id}', [CommentController::class, 'destroy'])->where(['post_id' => '[0-9]+', 'id' => '[0-9]+']);
    });
});
