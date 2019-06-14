<?php

use yii\db\Migration;

class m170911_035305_zones__remove_flats extends Migration
{
    public function safeUp()
    {
        $this->addColumn('zones__flats', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__flats', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));
        $this->alterColumn('zones__flats', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__flats', 'cas_user_id', 'DROP DEFAULT');

        $this->addColumn('zones__floors', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__floors', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));
        $this->alterColumn('zones__floors', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__floors', 'cas_user_id', 'DROP DEFAULT');

        $this->addColumn('zones__porches', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__porches', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));
        $this->alterColumn('zones__porches', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__porches', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__flats_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'flat_name' => $this->string()->notNull(),
            'floor_id' => $this->integer()->notNull(),
            'room_type' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->createTable('zones__floors_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'porch_id' => $this->string()->notNull(),
            'floor_name' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->createTable('zones__porches_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'porch_name' => $this->string()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);
    }   

    public function safeDown()
    {
        $this->dropColumn('zones__flats', 'created_at');
        $this->dropColumn('zones__flats', 'cas_user_id');

        $this->dropColumn('zones__floors', 'created_at');
        $this->dropColumn('zones__floors', 'cas_user_id');

        $this->dropColumn('zones__porches', 'created_at');
        $this->dropColumn('zones__porches', 'cas_user_id');

        $this->dropTable('zones__flats_history');
        $this->dropTable('zones__floors_history');
        $this->dropTable('zones__porches_history');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170911_035305_zones__remove_flats cannot be reverted.\n";

        return false;
    }
    */
}
