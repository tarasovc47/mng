<?php

use yii\db\Migration;

class m170823_060637_upgrade_docs_archive_1_1 extends Migration
{
    public function safeUp()
    {
        $this->createTable('docs_archive_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'doc_type_id' => $this->integer()->notNull(),
            'label' => $this->string()->notNull(),
            'descr' => $this->string(),
            'abonent' => $this->integer(),
            'client_id' => $this->string()->notNull(),
            'loki_basic_service_id' => $this->integer()->notNull(),
            'opened_at' => $this->integer()->notNull(),
            'extension' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('docs_types_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'available_for' => $this->integer()->notNull(),
            'folder' => $this->string()->notNull(),
            'sub_document' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->addColumn('docs_types', 'cas_user_id', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('docs_types', 'created_at', $this->integer()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropTable('docs_archive_history');
        $this->dropTable('docs_types_history');

        $this->dropColumn('docs_types', 'cas_user_id');
        $this->dropColumn('docs_types', 'created_at');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170823_060637_upgrade_docs_archive_1_1 cannot be reverted.\n";

        return false;
    }
    */
}
