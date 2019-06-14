<?php

use yii\db\Migration;

class m170725_045911_access_agreements_not_required extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('zones__access_agreements', 'name', 'DROP NOT NULL');
        $this->alterColumn('zones__access_agreements', 'extension', 'DROP NOT NULL');
    }

    public function safeDown()
    {
        echo "m170725_045911_access_agreements_not_required cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170725_045911_access_agreements_not_required cannot be reverted.\n";

        return false;
    }
    */
}
