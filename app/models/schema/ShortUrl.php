<?php

namespace app\models\schema;

use app\models\VisitLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "short_url".
 *
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property int|null $click_count
 * @property string|null $created_at
 *
 * @property VisitLog[] $visitLogs
 */
class ShortUrl extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'short_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['click_count'], 'default', 'value' => 0],
            [['original_url', 'short_code'], 'required'],
            [['original_url'], 'string'],
            [['original_url'], 'url',
                'defaultScheme' => 'http',
                'message' => 'Неверный формат URL'
            ],
            [['original_url'], 'validateUrlAccessibility'],
            [['click_count'], 'integer'],
            [['created_at'], 'safe'],
            [['short_code'], 'string', 'max' => 10],
            [['short_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'original_url' => 'Исходный Url',
            'short_code' => 'Короткий код',
            'click_count' => 'Счетчик кликов',
            'created_at' => 'Создано',
        ];
    }

    /**
     * Gets query for [[VisitLogs]].
     *
     * @return ActiveQuery
     */
    public function getVisitLogs(): ActiveQuery
    {
        return $this->hasMany(VisitLog::class, ['short_url_id' => 'id']);
    }

    public function validateUrlAccessibility($attribute): void
    {
        $url = $this->$attribute;

        try {
            $client = new Client();
            $response = $client->head($url, [
                'timeout' => 10,
                'allow_redirects' => [
                    'max' => 3
                ],
                'headers' => [
                    'User-Agent' => 'URL Shortener Service/1.0'
                ]
            ]);

            if ($response->getStatusCode() >= 400) {
                $this->addError($attribute, 'Данный URL не доступен');
            }
        } catch (RequestException|GuzzleException $e) {
            $this->addError($attribute, 'Данный URL не доступен');
        }
    }
}