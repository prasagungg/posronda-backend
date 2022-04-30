<?php

namespace App\Http\Services\Profile;

use App\Http\Services\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProfileQueries extends Service
{
    public function getProfileByUsername($username)
    {
        $user = User::username($username)->withCount(['posts', 'followers', 'following'])
            ->when(Auth::check(), function ($query) {
                $query->withCount(['followers as followed_by_me' => fn ($follow) => $follow->wherehas('user', fn ($user) => $user->where('id', self::$user->id))]);
            })
            ->first();
        if (empty($user)) throw new Exception("User not found", 404);

        $user->followed_by_me = $user->followed_by_me > 0;

        return $user;
    }
}
