<?php

namespace app\models\schema;

use app\models\ShortUrl;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "visit_log".
 *
 * @property int $id
 * @property int $short_url_id
 * @property string $ip_address
 * @property string|null $visited_at
 *
 * @property ShortUrl $shortUrl
 */
class VisitLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'visit_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['short_url_id', 'ip_address'], 'required'],
            [['short_url_id'], 'integer'],
            [['visited_at'], 'safe'],
            [['ip_address'], 'string', 'max' => 45],
            [['short_url_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShortUrl::class, 'targetAttribute' => ['short_url_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'short_url_id' => 'ID короткой ссылки',
            'ip_address' => 'IP адрес',
            'visited_at' => 'Время посещения',
        ];
    }

    /**
     * Gets query for [[ShortUrl]].
     *
     * @return ActiveQuery
     */
    public function getShortUrl(): ActiveQuery
    {
        return $this->hasOne(ShortUrl::class, ['id' => 'short_url_id']);
    }
}