<?php

namespace App\Http\Services\Comment;

use App\Http\Services\Service;
use App\Models\PostComment;

class CommentQueries extends Service
{
    public function getCommentByPost($post_id, $limit = 10, $orderby = 'created_at', $sort = 'desc', $filter = [])
    {
        $comments = PostComment::where('post_id', $post_id)->where('parent_id', null)
            ->with([
                'user:id,name,username,is_verified,profile_picture',
                'replies' => function ($query) {
                    $query->orderBy('created_at', 'ASC');
                },
                'replies.user:id,name,username,is_verified,profile_picture'
            ])
            ->withCount(['likes', 'likes as liked_by_me' => fn ($like) => $like->whereHas('user', fn ($user) => $user->where('id', self::$user->id))]);

        $sorted = $this->sorting($comments, $orderby, $sort, (new PostComment())->getTable());
        $filtered = $this->filter($sorted, $filter);
        $comments = $filtered->paginate($limit);

        $comments->getCollection()->transform(function ($comment) {
            $comment->liked_by_me = $comment->liked_by_me > 0;
            return $comment;
        });

        return $comments;
    }

    protected function filter($model, $filter = [])
    {
        if (count($filter) > 0) {
            return $model;
        } else {
            return $model;
        }
    }
}
