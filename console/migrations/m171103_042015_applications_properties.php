<?php

use yii\db\Schema;
use yii\db\Migration;

class m171103_042015_applications_properties extends Migration
{
    public function safeUp()
    {
        $this->createTable('applications_properties', [
            'id' => Schema::TYPE_PK,
            'application_event_id' => 'integer NOT NULL',
            'properties' => 'integer[] NOT NULL'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('applications_properties');
    }
}
