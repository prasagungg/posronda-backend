<?php

namespace App\Http\Services\Post;

use App\Http\Services\Service;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;

class PostQueries extends Service
{
    public function all($limit = 10)
    {
        $posts = Post::with(['user:id,username,is_verified', 'images.tags:id,post_image_id,user_id', 'images.tags.user:id,username'])
            ->withCount([
                'likes', 'comments',
                'likes as liked_by_me' => fn ($like) => $like->whereHas('user', fn ($user) => $user->where('id', self::$user->id))
            ])
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);

        $posts->getCollection()->transform(function ($post) {
            $post->liked_by_me = $post->liked_by_me > 0;
            return $post;
        });

        return $posts;
    }

    public function getPostByUsername($username, $limit = 9)
    {
        $posts = Post::whereHas('user', function ($query) use ($username) {
            $query->username($username);
        })
            ->with(['images.tags', 'images.tags.user:id,username'])
            ->withCount([
                'likes', 'comments',
                'likes as liked_by_me' => fn ($like) => $like->whereHas('user', fn ($user) => $user->where('id', self::$user->id))
            ])
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);

        $posts->getCollection()->transform(function ($post) {
            $post->liked_by_me = $post->liked_by_me > 0;
            return $post;
        });

        return $posts;
    }
}
