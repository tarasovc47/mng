<?php

use yii\db\Migration;
use yii\db\Schema;

class m171031_101438_properties extends Migration
{
    public function safeUp()
    {
        $this->createTable('properties',[
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'comment' => Schema::TYPE_TEXT,
            'parent_id' => Schema::TYPE_INTEGER,
            'sort' => Schema::TYPE_INTEGER,
            'application_type_id' => Schema::TYPE_INTEGER,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('properties');
    }
}
