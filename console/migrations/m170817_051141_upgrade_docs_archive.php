<?php

use yii\db\Migration;

class m170817_051141_upgrade_docs_archive extends Migration
{
    public function safeUp()
    {
        $this->addColumn('docs_archive', 'opened_at', $this->integer()->notNull());
        $this->renameColumn('docs_archive', 'user_id', 'loki_basic_service_id');
        $this->execute('ALTER TABLE docs_archive 
                            ALTER loki_basic_service_id TYPE integer USING (loki_basic_service_id::integer),
                            ALTER loki_basic_service_id DROP DEFAULT,
                            ALTER loki_basic_service_id SET NOT NULL');
        $this->addColumn('docs_archive', 'extension', $this->string());
    }

    public function safeDown()
    {
        echo "m170817_051141_upgrade_docs_archive cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170817_051141_upgrade_docs_archive cannot be reverted.\n";

        return false;
    }
    */
}
