<?php

namespace App\Http\Services\Comment;

use App\Http\Services\Service;
use App\Models\CommentLike;
use App\Models\PostComment;
use Exception;
use Illuminate\Support\Facades\DB;

class CommentCommands extends Service
{
    public function comment($post_id, $request)
    {
        try {
            DB::beginTransaction();

            $comment = new PostComment();
            $comment->post_id = $post_id;
            $comment->user_id = self::$user->id;
            $comment->comment = $request->comment;
            $comment->parent_id = $request->parent_id ?? null;

            if (!$comment->save()) {
                throw new Exception("Failed to comment", 400);
            }

            $comment = PostComment::with(['user:id,name,username,profile_picture'])->find($comment->id);

            DB::commit();
            return $comment;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function like($comment_id)
    {
        try {
            DB::beginTransaction();

            $like = new CommentLike();

            if (!empty($like->where('comment_id', $comment_id)->where('user_id', self::$user->id)->first())) {
                throw new Exception("You already liked this comment.", 200);
            }

            $like->comment_id = $comment_id;
            $like->user_id = self::$user->id;
            if (!$like->save()) {
                throw new Exception("Failed to like the comment");
            }

            $like = CommentLike::with(['user:id,username'])->find($like->id);

            DB::commit();
            return $like;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function unlike($comment_id)
    {
        try {
            DB::beginTransaction();

            $like = CommentLike::where('comment_id', $comment_id)->where('user_id', self::$user->id)->first();

            if (empty($like)) {
                throw new Exception("Comment not found", 404);
            }

            if (!$like->delete()) {
                throw new Exception("Failed to unlike the comment");
            }

            DB::commit();

            return 'ok';
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($post_id, $id)
    {
        try {
            DB::beginTransaction();

            $comment = PostComment::where('post_id', $post_id)->where('user_id', self::$user->id)->where('id', $id)->first();

            if (empty($comment)) {
                throw new Exception("Comment not found", 404);
            }

            if (!$comment->delete()) {
                throw new Exception("Failed to delete comment");
            }

            DB::commit();

            return "comment deleted";
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
