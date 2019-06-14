<?php

use yii\db\Migration;

class m170907_053409_upgrade_docs_archive_1_5 extends Migration
{
    public function safeUp()
    {
        $this->addColumn('docs_archive', 'publication_status', $this->integer()->notNull()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('docs_archive', 'publication_status');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170907_053409_upgrade_docs_archive_1_5 cannot be reverted.\n";

        return false;
    }
    */
}
