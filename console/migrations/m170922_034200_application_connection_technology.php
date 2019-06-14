<?php

use yii\db\Migration;

class m170922_034200_application_connection_technology extends Migration
{
    public function safeUp()
    {
        $this->addColumn('applications', 'connection_technology_id', $this->integer()->notNull());
        $this->addColumn('applications_history', 'connection_technology_id', $this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('applications', 'connection_technology_id');
        $this->dropColumn('applications_history', 'connection_technology_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170922_034200_application_connection_technology cannot be reverted.\n";

        return false;
    }
    */
}
