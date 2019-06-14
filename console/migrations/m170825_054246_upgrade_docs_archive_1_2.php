<?php

use yii\db\Migration;

class m170825_054246_upgrade_docs_archive_1_2 extends Migration
{
    public function safeUp()
    {
        $this->addColumn('docs_archive', 'billing_contract_id', $this->string());
        $this->addColumn('docs_archive', 'billing_contract_name', $this->string());
        $this->addColumn('docs_archive', 'billing_contract_date', $this->integer());
        $this->addColumn('docs_archive', 'billing_contract_type', $this->string());

        $this->addColumn('docs_archive_history', 'billing_contract_id', $this->string());
        $this->addColumn('docs_archive_history', 'billing_contract_name', $this->string());
        $this->addColumn('docs_archive_history', 'billing_contract_date', $this->integer());
        $this->addColumn('docs_archive_history', 'billing_contract_type', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('docs_archive', 'billing_contract_id');
        $this->dropColumn('docs_archive', 'billing_contract_name');
        $this->dropColumn('docs_archive', 'billing_contract_date');
        $this->dropColumn('docs_archive', 'billing_contract_type');

        $this->dropColumn('docs_archive_history', 'billing_contract_id');
        $this->dropColumn('docs_archive_history', 'billing_contract_name');
        $this->dropColumn('docs_archive_history', 'billing_contract_date');
        $this->dropColumn('docs_archive_history', 'billing_contract_type');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170825_054246_upgrade_docs_archive_1_2 cannot be reverted.\n";

        return false;
    }
    */
}
