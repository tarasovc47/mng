<?php

use yii\db\Migration;
use yii\db\Schema;

class m171004_061510_applications_comments extends Migration
{
    public function safeUp()
    {
        $this->createTable('applications_comments', [
            'id' => Schema::TYPE_PK,
            'application_event_id' => 'integer NOT NULL',
            'comment' => 'text NOT NULL'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('applications_comments');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171004_061510_applications_comments cannot be reverted.\n";

        return false;
    }
    */
}
