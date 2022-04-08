<?php

namespace App\Http\Services\Like;

use App\Http\Services\Service;
use App\Models\PostLike;

class LikeQueries extends Service
{
    public function getLikeByPost($post_id, $limit = 10)
    {
        $likes = PostLike::where('post_id', $post_id)
            ->with(['user:id,username,is_verified,profile_picture'])
            ->paginate($limit);

        return $likes;
    }
}
