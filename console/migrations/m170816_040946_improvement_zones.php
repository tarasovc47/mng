<?php

use yii\db\Migration;

class m170816_040946_improvement_zones extends Migration
{
    public function safeUp()
    {
        $this->addColumn('zones__addresses', 'contract_with_manag_company', $this->integer()->notNull()->defaultValue('0'));
        
        $this->addColumn('docs_types', 'sub_document', $this->integer()->notNull()->defaultValue('0'));
    }

    public function safeDown()
    {
        echo "m170816_040946_improvement_zones cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170816_040946_improvement_zones cannot be reverted.\n";

        return false;
    }
    */
}
