<?php

use yii\db\Migration;

class m171020_035515_addresses_recicle extends Migration
{
    public function safeUp()
    {
        $this->createTable('addresses_recycle',[
            'id' => $this->primaryKey(),
            'billing_base_clients_id' => $this->integer(),
            'billing_base_clients_client_id' => $this->string(),
            'billing_base_clients_oper_id' => $this->string(),
            'billing_company_name' => $this->string(),
            'billing_company_inn' => $this->string(),
            'billing_company_address_jur' => $this->string(),
            'dadata_clean_address' => $this->string(),
            'dadata_clean_address_fias_id' => $this->string(),
            'dadata_clean_address_status' => $this->integer(),
            'dadata_suggest_address' => $this->string(),
            'dadata_suggest_address_fias_id' => $this->string(),
            'dadata_suggest_company_name' => $this->string(),
            'conclusion_address' => $this->integer()->notNull(),
            'conclusion_company_name' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        //echo "m171020_035515_addresses_recicle cannot be reverted.\n";

        $this->dropTable('addresses_recycle');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171020_035515_addresses_recicle cannot be reverted.\n";

        return false;
    }
    */
}
