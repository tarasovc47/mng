<?php

use yii\db\Schema;
use yii\db\Migration;

class m170831_054853_applications_attributes_rework extends Migration
{
    public function safeUp()
    {
        $this->createTable('applications_attributes', [
            'id' => Schema::TYPE_PK,
            'application_event_id' => 'integer NOT NULL',
            'attributes' => 'integer[] NOT NULL'
        ]);

        $this->dropColumn('applications', 'attributes');
        $this->dropColumn('applications_history', 'attributes');
    }

    public function safeDown()
    {
        $this->dropTable('applications_attributes');
        $this->addColumn('applications', 'attributes', 'integer[] NOT NULL');
        $this->addColumn('applications_history', 'attributes', 'integer[] NOT NULL');
    }
}
