<?php

use yii\db\Migration;

class m171124_092139_upgrade_tariffs__speed extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tariffs', 'speed', $this->integer());
        $this->addColumn('tariffs', 'channels', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('tariffs', 'speed');
        $this->dropColumn('tariffs', 'channels');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171124_092139_upgrade_tariffs__speed cannot be reverted.\n";

        return false;
    }
    */
}
