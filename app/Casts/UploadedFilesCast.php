<?php
namespace App\Casts;

use App\Models\Attachments;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class UploadedFilesCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if(self::isJson($value)) {
            $arr = json_decode($value, true);
            if (is_array($arr) && !empty($arr)) {
                return Attachments::whereIn('id', $arr)->get();
            }
        }

        return false;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if(is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }

    public static function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
