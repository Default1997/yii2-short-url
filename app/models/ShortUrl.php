<?php

namespace app\models;

use app\models\schema\ShortUrl as ShortUrlSchema;
use Random\RandomException;
use Yii;

class ShortUrl extends ShortUrlSchema
{
    /**
     * @throws RandomException
     */
    public static function generateShortCode(): string
    {
        do {
            $randomNumber = base_convert(bin2hex(random_bytes(6)), 16, 36);
            $code = str_pad(substr($randomNumber, 0, 6), 6, '0');
            $code = strtoupper($code);
        } while (self::findOne(['short_code' => $code]));

        return $code;
    }

    public function getShortUrl(): string
    {
        $request = Yii::$app->request;
        return sprintf(
            '%s/%s',
            rtrim($request->hostInfo, '/'),
            $this->short_code
        );
    }

    public function incrementClickCount(): bool
    {
        return $this->updateCounters(['click_count' => 1]) !== false;
    }
}
