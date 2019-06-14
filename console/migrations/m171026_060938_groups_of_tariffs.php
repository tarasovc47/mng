<?php

use yii\db\Migration;

class m171026_060938_groups_of_tariffs extends Migration
{
    public function safeUp()
    {
        $this->createTable('tariffs_groups', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
        $this->createTable('tariffs_groups_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('tariffs_to_groups', [
            'id' => $this->primaryKey(),
            'tariffs_group_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
        $this->createTable('tariffs_to_groups_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'tariffs_group_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        //echo "m171026_060938_groups_of_tariffs cannot be reverted.\n";
        $this->dropTable('tariffs_groups');
        $this->dropTable('tariffs_groups_history');

        $this->dropTable('tariffs_to_groups');
        $this->dropTable('tariffs_to_groups_history');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171026_060938_groups_of_tariffs cannot be reverted.\n";

        return false;
    }
    */
}
