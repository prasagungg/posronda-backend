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
        $posts = Post::with(['user:id,username,is_verified', 'images.tags', 'images.tags.user:id,username'])->withCount(['likes', 'comments'])->paginate($limit);

        return $posts;
    }
}
