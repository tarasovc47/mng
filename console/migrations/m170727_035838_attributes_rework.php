<?php

use yii\db\Migration;

class m170727_035838_attributes_rework extends Migration
{
    public function safeUp()
    {
        $this->addColumn('attributes', 'department_id', $this->integer());
        $this->dropColumn('attributes', 'type');
        $this->update('attributes', [ 'department_id' => 1 ]);
        $this->alterColumn('attributes', 'department_id', 'SET NOT NULL');
    }

    public function safeDown()
    {
        echo "m170727_035838_attributes_rework cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170727_035838_attributes_rework cannot be reverted.\n";

        return false;
    }
    */
}
