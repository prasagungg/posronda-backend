<?php

namespace App\Http\Services\Profile;

use App\Http\Services\Service;
use App\Models\User;
use Exception;

class ProfileQueries extends Service
{
    public function getProfileByUsername($username)
    {
        $user = User::username($username)->withCount(['posts'])->first();
        if (empty($user)) {
            throw new Exception("User not found", 404);
        }

        return $user;
    }
}
