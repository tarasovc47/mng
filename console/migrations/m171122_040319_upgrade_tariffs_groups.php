<?php

use yii\db\Migration;

class m171122_040319_upgrade_tariffs_groups extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tariffs_groups', 'abonent_type', $this->integer()->notNull());
        $this->addColumn('tariffs_groups_history', 'abonent_type', $this->integer()->notNull());

        $this->createTable('zones__addresses_to_tariffs_groups', [
        	'id' => $this->primaryKey(),
        	'address_id' => $this->integer()->notNull(),
        	'tariffs_group_id' => $this->integer()->notNull(),
        	'abonent_type' => $this->integer()->notNull(),
        	'publication_status' => $this->integer()->notNull(),
        	'created_at' => $this->integer()->notNull(),
        	'cas_user_id' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropColumn('tariffs_groups', 'abonent_type');
        $this->dropColumn('tariffs_groups_history', 'abonent_type');
        $this->dropTable('zones__addresses_to_tariffs_groups');

        return true;
    }
}
