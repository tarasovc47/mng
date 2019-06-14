<?php

use yii\db\Migration;

class m170901_052109_upgrade_docs_archive_1_3 extends Migration
{
    public function safeUp()
    {
        $this->createTable('docs_archive_to_loki_basic_services', [
            'id' => $this->primaryKey(),
            'doc_id' => $this->integer()->notNull(),
            'loki_basic_service_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
        $this->createTable('docs_archive_to_loki_basic_services_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'doc_id' => $this->integer()->notNull(),
            'loki_basic_service_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->dropColumn('docs_archive', 'loki_basic_service_id');
        $this->dropColumn('docs_archive_history', 'loki_basic_service_id');
    }

    public function safeDown()
    {
        $this->dropTable('docs_archive_to_loki_basic_services');
        $this->dropTable('docs_archive_to_loki_basic_services_history');

        $this->addColumn('docs_archive', 'loki_basic_service_id', $this->integer()->defaultValue(-1));
        $this->addColumn('docs_archive_history', 'loki_basic_service_id', $this->integer()->defaultValue(-1));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170901_052109_upgrade_docs_archive_1_3 cannot be reverted.\n";

        return false;
    }
    */
}
