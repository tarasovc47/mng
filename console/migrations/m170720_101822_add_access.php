<?php

use yii\db\Migration;

class m170720_101822_add_access extends Migration
{
    public function safeUp()
    {
        $this->insert('modules_settings', [
            'id' => '13',
            'name' => 'Передать в другой отдел',
            'descr' => 'Только для собственных заявок',
            'module_id' => 1
        ]);

        $this->insert('access', [
            'cas_user_id' => 0,
            'department_id' => 3,
            'module_setting_id' => 13,
            'value' => 2,
        ]);
    }

    public function safeDown()
    {
        echo "m170720_101822_add_access cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170720_101822_add_access cannot be reverted.\n";

        return false;
    }
    */
}
