<?php

use yii\db\Migration;

class m171120_105934_ticket_table_create extends Migration
{
    public function safeUp()
    {
        $this->createTable('tickets', [
            'name' => $this->string()->notNull(),
            'planned_start_on' => $this->integer()->notNull(),
            'start_on' => $this->integer()->notNull(),
            'planned_end_on' => $this->integer(),
            'end_on' => $this->integer(),
            'description' => $this->text(),
            'on_complete' => $this->text(),
            'stalled_ticket_id' => $this->integer(),
            'parent_ticket_id' => $this->integer(),
            'user_id' => $this->integer()->notNull(),
            'author_id' => $this->integer(),
            'reviewer_id' => $this->integer(),
            'ticketable_id' => $this->integer(),
            'ticketable_type' => $this->string(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('tickets');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171120_105934_ticket_table_create cannot be reverted.\n";

        return false;
    }
    */
}
