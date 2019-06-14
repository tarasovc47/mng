<?php

use yii\db\Migration;

class m171101_034006_addresses_recycle_postcode extends Migration
{
    public function safeUp()
    {
        $this->addColumn('addresses_recycle', 'postcode', $this->string());
    }

    public function safeDown()
    {
        //echo "m171101_034006_addresses_recycle_postcode cannot be reverted.\n";
        $this->dropColumn('addresses_recycle', 'postcode');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171101_034006_addresses_recycle_postcode cannot be reverted.\n";

        return false;
    }
    */
}
