<?php

use yii\db\Migration;
use yii\db\Schema;

class m171012_073828_rework_idea_of_brigades_to_groups extends Migration
{
    public function safeUp()
    {
        $this->dropTable('brigades');
        $this->dropTable('brigades_members');
        $this->dropTable('brigadiers');

        $this->createTable('users_groups',[
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'head_id' => Schema::TYPE_INTEGER,
            'department_id' => 'integer NOT NULL',
        ]);

        $this->addColumn('cas_user', 'group_id', $this->integer()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        echo "m171012_073828_rework_idea_of_brigades_to_groups cannot be reverted.\n";

        return false;
    }
}
