<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%visit_log}}`.
 */
class m260314_103532_create_visit_log_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('visit_log', [
            'id' => $this->primaryKey(),
            'short_url_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'visited_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-visit_log-short_url_id', 'visit_log', 'short_url_id');
        $this->createIndex('idx-visit_log-ip_address', 'visit_log', 'ip_address');
        $this->createIndex('idx-visit_log-visited_at', 'visit_log', 'visited_at');

        $this->addForeignKey(
            'fk-visit_log-short_url_id',
            'visit_log',
            'short_url_id',
            'short_url',
            'id',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-visit_log-short_url_id', 'visit_log');
        $this->dropTable('visit_log');
    }
}
