<?php

use yii\db\Migration;

class m170905_053825_upgrade_docs_archive_1_4 extends Migration
{
    public function safeUp()
    {
        $this->createTable('docs_archive_to_services', [
            'id' => $this->primaryKey(),
            'doc_id' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
        $this->createTable('docs_archive_to_services_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'doc_id' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
        $this->createTable('docs_archive_to_connection_technologies', [
            'id' => $this->primaryKey(),
            'doc_id' => $this->integer()->notNull(),
            'conn_tech_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
        $this->createTable('docs_archive_to_connection_technologies_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'doc_id' => $this->integer()->notNull(),
            'conn_tech_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('docs_archive_to_services');
        $this->dropTable('docs_archive_to_services_history');

        $this->dropTable('docs_archive_to_connection_technologies');
        $this->dropTable('docs_archive_to_connection_technologies_history');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170905_053825_upgrade_docs_archive_1_4 cannot be reverted.\n";

        return false;
    }
    */
}
