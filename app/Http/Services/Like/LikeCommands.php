<?php

namespace App\Http\Services\Like;

use App\Http\Services\Service;
use App\Models\PostLike;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikeCommands extends Service
{
    public function like($post_id)
    {
        try {
            DB::beginTransaction();

            $like = new PostLike();
            $like->post_id = $post_id;
            $like->user_id = self::$user->id;
            if (!$like->save()) {
                throw new Exception("Failed to like the post");
            }

            $like = PostLike::with(['user:id,username'])->find($like->id);

            DB::commit();
            return $like;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function unlike($post_id)
    {
        try {
            DB::beginTransaction();

            $like = PostLike::where('post_id', $post_id)->where('user_id', self::$user->id)->first();

            if (empty($like)) {
                throw new Exception("Data not found", 404);
            }

            if (!$like->delete()) {
                throw new Exception("Failed to unlike the post");
            }

            DB::commit();

            return 'ok';
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
