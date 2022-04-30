<?php

namespace App\Http\Services\Follower;

use App\Http\Services\Service;
use App\Models\Follower;

class FollowerQueries extends Service
{
    public function getFollowers($user_id, $limit = 10, $orderby = 'created_at', $sort = 'desc', $filter = [])
    {
        $followers = Follower::where('followed_id', $user_id)->with(['user:id,name,username,profile_picture,is_verified,is_active']);

        $filtered = $this->filter($followers, $filter, 'user');
        $sorted = $this->sorting($filtered, $orderby, $sort, (new Follower())->getTable());
        $followers = $sorted->paginate($limit);

        return $followers;
    }

    public function getFollowings($user_id, $limit = 10, $orderby = 'created_at', $sort = 'desc', $filter = [])
    {
        $followings = Follower::where('user_id', $user_id)->with(['followed:id,name,username,profile_picture,is_verified,is_active']);

        $filtered = $this->filter($followings, $filter, 'followed');
        $sorted = $this->sorting($filtered, $orderby, $sort, (new Follower())->getTable());
        $followings = $sorted->paginate($limit);

        return $followings;
    }

    protected function filter($model, $filter = [], $relation)
    {
        if (count($filter) == 0) {
            return $model;
        }

        $username = isset($filter['username']) ? $filter['username'] : null;
        $name = isset($filter['name']) ? $filter['name'] : null;

        $model = $model
            ->when(!empty($username), function ($query) use ($username, $relation) {
                $query->whereHas($relation, function ($user) use ($username) {
                    $user->where('username', 'like', "%{$username}%");
                });
            })
            ->when(!empty($name), function ($query) use ($name, $relation) {
                $query->whereHas($relation, function ($user) use ($name) {
                    $user->where('name', 'like', "%{$name}%");
                });
            });

        return $model;
    }
}
