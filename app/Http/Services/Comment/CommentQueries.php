<?php

namespace App\Http\Services\Comment;

use App\Http\Services\Service;
use App\Models\PostComment;

class CommentQueries extends Service
{
    public function getCommentByPost($post_id, $limit = 10, $orderby = null, $sort = 'asc', $filter = [])
    {
        $comments = PostComment::where('post_id', $post_id)->where('parent_id', null)
            ->with(['user:id,username,is_verified,profile_picture', 'replies']);

        $sorted = $this->sorting($comments, $orderby, $sort, (new PostComment())->getTable());
        $filtered = $this->filter($sorted, $filter);
        $comments = $filtered->paginate($limit);

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
