<?php

use yii\db\Migration;

class m170728_112611_tariff_price extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tariffs', 'price', $this->integer()->notNull()->defaultValue('0'));
    }

    public function safeDown()
    {
        echo "m170728_112611_tariff_price cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170728_112611_tariff_price cannot be reverted.\n";

        return false;
    }
    */
}
