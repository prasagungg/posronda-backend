<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Ramsey\Uuid\Uuid;

class FirebaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:200000,1');
        $this->middleware('auth:api', ['only' => 'upload']);
    }
    public function upload(Storage $storage)
    {
        $stricted_size_mimes = []; // ['pdf']; //^ mime file yang ingin dibatasi ukurannya. (file dengan tipe mime image tidak akan diberi batasan ukuran, karena akan di intervention)
        $validator = $this->validateUpload(request(), $this->acceptable_mimes, $stricted_size_mimes);

        if ($validator->fails()) {
            return response()->error($validator->errors());
        }

        try {
            $ext = request()->file('file')->getClientOriginalExtension();

            $file = $this->imageHandler(request()->file('file'));

            $upload_res = FirebaseHelper::upload($file, $storage, $ext, $this->sub_dir[request('type')], true);

            return response()->withKey($upload_res, 'path');
        } catch (Exception $e) {
            if (in_array($e->getCode(), $this->error_codes)) {
                return response()->error($e->getMessage(), $e->getCode());
            }

            return response()->error($e->getMessage(), 500);
        }
    }

    public function getImageUri($type, $image_path)
    {
        if (!in_array(request()->route('type'), ['download', 'load'])) {
            return response()->error('Url parameter must be one of the following: load, download');
        }

        try {
            $get_file = FirebaseHelper::getImageUri($image_path);

            $ext = substr($image_path, strpos($image_path, '.') + 1);
            $temp_name = now()->timestamp . Uuid::uuid1() . '.' . $ext;
            $temp_image = tempnam(sys_get_temp_dir(), $temp_name);
            // If file pdf
            // $headers = [
            //     'Content-Type' => 'application/pdf',
            // ];
            copy($get_file, $temp_image);

            if ($type == 'download') {
                return response()->download($temp_image, $temp_name, []);
            }

            return response()->file($temp_image);
        } catch (Exception $e) {
            if (in_array($e->getCode(), $this->error_codes)) {
                return response()->json(['success' => false, 'message' => 'Error', 'data' => $e->getMessage()], $e->getCode());
            }

            return response()->json(['success' => false, 'message' => 'Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function imageHandler($payload)
    {
        $image = Image::make($payload);
        $image->resize(480, 480, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image;
    }

    private function validateUpload($request, $mimes, $stricted_mimes)
    {
        return Validator::make($request->all(), [
            'file' => ['required', 'mimes:' . implode(',', $mimes), function ($attr, $val, $fail) use ($request, $stricted_mimes) {
                if (in_array(request()->file('file')->getClientOriginalExtension(), $stricted_mimes)) {
                    (($request->file('file')->getSize() / 1024 | 0) > 2048) ? $fail('The ' . $attr . ' size must not be greater than 2 MB') : null;
                }
            }],
            'type' => ['required', 'in:' . implode(',', (array) array_keys($this->sub_dir))]
        ], [
            'type.required' => 'url param \':attribute\' is required',
            'type.in' => 'the type must be one of the following : ' . implode(', ', (array) array_keys($this->sub_dir))
        ]);
    }
}
