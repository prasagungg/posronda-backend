<?php

namespace App\Http\Services\User;

use App\Http\Services\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UserCommands extends Service
{
    public function create($data)
    {
        try {
            DB::beginTransaction();

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->profile_picture = self::DEFAULT_PROFILE_PICTURE;

            DB::commit();

            if (!$user->save()) {
                return false;
            }

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            if (in_array($e->getCode(), self::$error_codes)) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function update($id, $data)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);
            if (empty($user)) {
                throw new Exception("User not found", 404);
            }

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->phone = isset($data['phone']) ? $data['phone'] : null;
            $user->gender = isset($data['gender']) ? $data['gender'] : null;
            $user->profile_picture = isset($data['profile_picture']) ? $data['profile_picture'] : self::DEFAULT_PROFILE_PICTURE;
            $user->bio = isset($data['bio']) ? $data['bio'] : null;
            $user->website = isset($data['website']) ? $data['website'] : null;

            if (!$user->save()) {
                return false;
            }

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            if (in_array($e->getCode(), self::$error_codes)) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);
            if (empty($user)) {
                throw new Exception("User not found", 404);
            }

            if (!$user->delete()) {
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            if (in_array($e->getCode(), self::$error_codes)) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function verify($id, $verified = false)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);
            if (empty($user)) {
                throw new Exception("User not found", 404);
            }

            $user->is_verified = $verified;

            if (!$user->save) {
                return false;
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            if (in_array($e->getCode(), self::$error_codes)) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            throw new Exception($e->getMessage(), 500);
        }
    }
}
