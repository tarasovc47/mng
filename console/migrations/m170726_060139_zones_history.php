<?php

use yii\db\Migration;

class m170726_060139_zones_history extends Migration
{
    public function safeUp()
    {
        $this->createTable('zones__districts_and_areas_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'type' => $this->smallInteger()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'brigade' => $this->integer(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('zones__access_agreements_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string(),
            'label' => $this->string()->notNull(),
            'extension' => $this->string(),
            'oper_id' => $this->integer()->notNull(),
            'manag_company_id' => $this->integer()->notNull(),
            'opened_at' => $this->integer()->notNull(),
            'closed_at' => $this->integer()->notNull(),
            'auto_prolongation' => $this->integer()->notNull(),
            'comment' => $this->text(),
            'rent_price' => $this->double()->notNull(),
            'price_is_ratio' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->alterColumn('zones__access_agreements', 'comment', 'DROP NOT NULL');

        $this->createTable('contacts_offices_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'publication_status' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        echo "m170726_060139_zones_history cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170726_060139_zones_history cannot be reverted.\n";

        return false;
    }
    */
}
