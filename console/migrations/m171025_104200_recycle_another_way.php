<?php

use yii\db\Migration;

class m171025_104200_recycle_another_way extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('addresses_recycle', 'conclusion_address', 'DROP NOT NULL');
        $this->alterColumn('addresses_recycle', 'conclusion_company_name', 'DROP NOT NULL');
        $this->alterColumn('addresses_recycle', 'billing_base_clients_id', 'SET NOT NULL');
    }

    public function safeDown()
    {
        echo "m171025_104200_recycle_another_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171025_104200_recycle_another_way cannot be reverted.\n";

        return false;
    }
    */
}
