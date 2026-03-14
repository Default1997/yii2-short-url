<?php

namespace app\models;

use app\models\schema\VisitLog as VisitLogSchema;
use yii\db\Exception;

class VisitLog extends VisitLogSchema
{
    /**
     * @throws Exception
     */
    public static function logVisit(int $shortUrlId, string $ipAddress): bool
    {
        $visitLog = new self();
        $visitLog->short_url_id = $shortUrlId;
        $visitLog->ip_address = $ipAddress;
        $visitLog->visited_at = date('Y-m-d H:i:s');

        return $visitLog->save();
    }
}
