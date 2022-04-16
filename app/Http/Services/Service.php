<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Service
{
    static $curl, $user, $base_url;

    static $error_codes = [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451, 500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511];

    static function init()
    {
        self::$curl = new Client();
        self::$user = Auth::user();
        self::$base_url = env('APP_URL');
    }

    const DEFAULT_PROFILE_PICTURE = 'https://instagram.fupg2-2.fna.fbcdn.net/v/t51.2885-19/44884218_345707102882519_2446069589734326272_n.jpg?efg=eyJybWQiOiJpZ19hbmRyb2lkX21vYmlsZV9uZXR3b3JrX3N0YWNrX3JldHJ5X3RpbWVvdXQ6cmV0cnlfYm90aCJ9&_nc_ht=instagram.fupg2-2.fna.fbcdn.net&_nc_cat=1&_nc_ohc=D2tefvsAbhsAX9HeZ8V&edm=AEVnrqQBAAAA&ccb=7-4&ig_cache_key=YW5vbnltb3VzX3Byb2ZpbGVfcGlj.2-ccb7-4&oh=00_AT_Do5kvQx-89WmrWShbQPpnkKxP4CtZUDlC1cqaPsH2zw&oe=625C20CF&_nc_sid=3ae735';

    protected function sorting($model, $orderby = null, $sort = 'asc', $table)
    {
        if ($orderby != null) {
            $attributes = Schema::getColumnListing($table);
            $sort = in_array($sort, ['asc', 'desc', 'ASC', 'DESC']) ? $sort : 'asc';

            $model = $model->when(in_array($orderby, $attributes), function ($query) use ($orderby, $sort) {
                $query->orderBy($orderby, $sort);
            });

            return $model;
        } else {
            return $model;
        }
    }
}
