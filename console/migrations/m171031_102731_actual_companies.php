<?php

use yii\db\Migration;

class m171031_102731_actual_companies extends Migration
{
    public function safeUp()
    {
        $this->addColumn('addresses_recycle', 'actual_company', $this->integer());
    }

    public function safeDown()
    {
        //echo "m171031_102731_actual_companies cannot be reverted.\n";

        $this->dropColumn('addresses_recycle', 'actual_company');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171031_102731_actual_companies cannot be reverted.\n";

        return false;
    }
    */
}
