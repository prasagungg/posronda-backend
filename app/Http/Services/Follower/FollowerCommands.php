<?php

namespace App\Http\Services\Follower;

use App\Http\Services\Service;
use App\Models\Follower;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class FollowerCommands extends Service
{
    public function follow($user_id)
    {
        try {
            DB::beginTransaction();

            if (empty((new User())->find($user_id))) throw new Exception("User not found", 404);

            $follow = Follower::create([
                'user_id' => self::$user->id,
                'followed_id' => $user_id,
            ]);

            if (empty($follow)) {
                throw new Exception("Failed to follow");
            }

            DB::commit();

            return 'ok';
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function unfollow($user_id)
    {
        try {
            DB::beginTransaction();

            if (empty((new User())->find($user_id))) throw new Exception("User not found", 404);

            $followed = Follower::where('user_id', self::$user->id)->where('followed_id', $user_id)->first();

            if (empty($followed)) throw new Exception("user followed not found", 404);

            if (!$followed->delete()) throw new Exception("failed to unfollow");

            DB::commit();
            return 'ok';
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
