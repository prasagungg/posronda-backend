<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage as LaravelStorage;
use Ramsey\Uuid\Uuid;

class FirebaseHelper extends Helper
{
    static $collection = 'Pos-Ronda';

    public static function upload($payload, $storage, $extension, $sub_dir, $get_uploaded_image = false)
    {
        try {
            //^ Masih error kalo pake kode dibawah ini
            // $storage->getBucket();
            // $storage->getStorageClient();

            $uuid = now()->timestamp . Uuid::uuid4();
            $foto = app('firebase.firestore')->database()->collection(self::$collection)->document($uuid);
            $firebase_storage_path = self::$collection . '/' . $sub_dir . '/';
            $name = $foto->id();

            $localfolder = storage_path('app/uploads/firebase-temp-uploads/');
            $file = $name . '.' . $extension;
            $storage_path = 'uploads/firebase-temp-uploads';

            if (!LaravelStorage::disk('local')->exists($storage_path . '/')) {
                LaravelStorage::disk('local')->makeDirectory($storage_path);
            }

            (get_class($payload) == "Intervention\Image\Image") ? $payload->save($localfolder . $file) : $payload->storeAs('uploads/firebase-temp-uploads/', $file);

            $uploaded_file = fopen($localfolder . $file, 'r');
            $upload_cloud = $firebase_storage_path . $file;
            app('firebase.storage')->getBucket()->upload($uploaded_file, ['name' => $firebase_storage_path . $file]);

            if (LaravelStorage::disk('local')->exists($storage_path . '/' . $file)) {
                LaravelStorage::delete($storage_path . '/' . $file);
            }

            if ($get_uploaded_image) {
                return str_replace("/", "~", $upload_cloud);
            }

            return 'File berhasil diupload';
        } catch (Exception $e) {
            if (LaravelStorage::disk('local')->exists($storage_path . '/' . $file)) {
                LaravelStorage::delete($storage_path . '/' . $file);
            }

            if (in_array($e->getCode(), self::$error_codes)) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            throw new Exception($e->getMessage(), 500);
        }
    }

    public static function getImageUri($image_path)
    {
        // $storage->getBucket()->object($upload_cloud)->signedUrl(Carbon::now('Asia/Jakarta')->addMinutes(60));
        $image_path = str_replace("~", "/", $image_path);

        try {
            $expiredAt = Carbon::now('Asia/Jakarta')->addMinutes(60);
            $imageReference = app('firebase.storage')->getBucket()->object($image_path);

            if ($imageReference->exists()) {
                return $imageReference->signedUrl($expiredAt);
            }

            throw new Exception("Firebase: file tidak ditemukan");
        } catch (Exception $e) {
            if (in_array($e->getCode(), self::$error_codes)) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            throw new Exception($e->getMessage(), 500);
        }
    }
}
