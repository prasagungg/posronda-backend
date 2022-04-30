<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\FirebaseController;
use App\Http\Controllers\Api\V1\FollowersController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
    abort(404);
    // return response()->error("The page you are looking for is not available", 404);
});

Route::get('clear-cache', function () {
    Artisan::call('cache:clear');

    return response()->json(['message' => 'cache cleared successfully!']);
});

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'userProfile']);

        // Registration
        Route::post('register', [AuthController::class, 'register']);
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('{username}', [ProfileController::class, 'getProfileByUsername']);
    });

    // Post
    Route::prefix('post')->group(function () {
        Route::get('', [PostController::class, 'all']);
        Route::post('', [PostController::class, 'create']);
        Route::get('{username}', [PostController::class, 'getByUsername']);
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

        // Like Comment
        Route::post('{comment_id}/like', [CommentController::class, 'like'])->where('comment_id', '[0-9]+');
        Route::post('{comment_id}/unlike', [CommentController::class, 'unlike'])->where('comment_id', '[0-9]+');
    });

    Route::prefix('cdn')->group(function () {
        Route::post('upload', [FirebaseController::class, 'upload']);
        Route::get('file/{type}/{path}', [FirebaseController::class, 'getImageUri']);
    });

    // Search
    Route::get('search', [SearchController::class, 'search']);

    // Follower
    Route::prefix('friendships')->group(function () {
        Route::post('{user_id}/follow', [FollowersController::class, 'follow'])->where('user_id', '[0-9]+');
        Route::post('{user_id}/unfollow', [FollowersController::class, 'unfollow'])->where('user_id', '[0-9]+');
        Route::get('{user_id}/followers', [FollowersController::class, 'getFollowers'])->where('user_id', '[0-9]+');
        Route::get('{user_id}/followings', [FollowersController::class, 'getFollowings'])->where('user_id', '[0-9]+');
    });
});
