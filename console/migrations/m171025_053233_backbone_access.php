<?php

use yii\db\Migration;

class m171025_053233_backbone_access extends Migration
{
    public function safeUp()
    {
        $this->insert('modules_settings', [
            'name' => 'Просмотр ARP опорной сети',
            'descr' => 'Доступ к просмотру ARP опорной сети',
            'module_id' => 3,
        ]);

        $this->insert('access', [
            'cas_user_id' => 0,
            'department_id' => 3,
            'module_setting_id' => 21,
            'value' => 2,
            'descr' => 'Доступ к просмотру ARP опорной сети',
        ]);
    }

    public function safeDown()
    {
        echo "m171025_053233_backbone_access cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171025_053233_backbone_access cannot be reverted.\n";

        return false;
    }
    */
}
