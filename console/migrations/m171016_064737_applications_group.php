<?php

use yii\db\Migration;

class m171016_064737_applications_group extends Migration
{
    public function safeUp()
    {
        $this->addColumn('applications', 'group_id', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('applications_history', 'group_id', $this->integer()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('applications', 'group_id');
        $this->dropColumn('applications_history', 'group_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171016_064737_applications_group cannot be reverted.\n";

        return false;
    }
    */
}
