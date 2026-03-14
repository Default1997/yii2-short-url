<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%short_urls}}`.
 */
class m260314_101850_create_short_urls_table extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('short_url', [
            'id' => $this->primaryKey(),
            'original_url' => $this->text()->notNull(),
            'short_code' => $this->string(10)->notNull()->unique(),
            'click_count' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx-short_urls-short_code', 'short_url', 'short_code');
    }

    public function safeDown(): void
    {
        $this->dropTable('short_url');
    }
}
