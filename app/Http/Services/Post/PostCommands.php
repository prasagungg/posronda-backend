<?php

namespace App\Http\Services\Post;

use App\Http\Services\Service;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostImageTag;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostCommands extends Service
{
    public function create($data)
    {
        try {
            DB::beginTransaction();

            $post = new Post();
            $post->user_id = Auth::user()->id;
            $post->caption = $data->caption;

            if (!$post->save()) {
                throw new Exception("Failed to save data");
            }

            foreach ($data->images as $i => $image) {
                $post_image = new PostImage();
                $post_image->post_id = $post->id;
                $post_image->url_image = self::$base_url . sprintf("/%s/%s", "v1/cdn/file/load", $image['path']);
                if (!$post_image->save()) {
                    DB::rollBack();
                    throw new Exception("Failed to save data");
                }

                foreach ($image['tags'] as $user_id) {
                    $post_image_tag = new PostImageTag();
                    $post_image_tag->post_image_id = $post_image->id;
                    $post_image_tag->user_id = $user_id;
                    if (!$post_image_tag->save()) {
                        DB::rollBack();
                        throw new Exception("Failed to save data");
                    }
                }
            }

            $post = Post::with(['user:id,username', 'images:id,post_id,url_image', 'images.tags:id,post_image_id,user_id', 'images.tags.user:id,username'])->find($post->id);

            DB::commit();

            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
