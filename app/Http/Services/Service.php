<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Service
{
    static $curl, $user, $base_url;
    static $DEFAULT_PROFILE_PICTURE;

    static $error_codes = [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451, 500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511];

    static function init()
    {
        self::$curl = new Client();
        self::$user = Auth::user();
        self::$base_url = env('APP_URL');
        self::$DEFAULT_PROFILE_PICTURE = sprintf('%s/%s/%s', env('APP_URL'), 'v1/cdn/file/load', 'Pos-Ronda~users~1651282790ddfed96d-ffd6-4ec4-9ace-70b2e7d89db6.png');
    }

    protected function sorting($model, $orderby = null, $sort = 'asc', $table)
    {
        if ($orderby == null) return $model;

        $attributes = Schema::getColumnListing($table);
        $sort = in_array($sort, ['asc', 'desc', 'ASC', 'DESC']) ? $sort : 'asc';

        $model = $model->when(in_array($orderby, $attributes), function ($query) use ($orderby, $sort) {
            $query->orderBy($orderby, $sort);
        });

        return $model;
    }
}
