<?php

use yii\db\Migration;

class m171016_041401_brigade_to_group_at_zones extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('zones__districts_and_areas', 'brigade', 'users_group_id');
        $this->renameColumn('zones__districts_and_areas_history', 'brigade', 'users_group_id');
    }

    public function safeDown()
    {
        //echo "m171016_041401_brigade_to_group_at_zones cannot be reverted.\n";

        $this->renameColumn('zones__districts_and_areas', 'users_group_id', 'brigade');
        $this->renameColumn('zones__districts_and_areas_history', 'users_group_id', 'brigade');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171016_041401_brigade_to_group_at_zones cannot be reverted.\n";

        return false;
    }
    */
}
