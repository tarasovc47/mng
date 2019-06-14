<?php

use yii\db\Migration;

class m170919_045857_zones__tariffs_history extends Migration
{
    public function safeUp()
    {
        $this->createTable('tariffs_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'for_abonent_type' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'closed_at' => $this->integer(),
            'package' => $this->integer()->notNull(),
            'opened_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'priority' => $this->integer()->notNull(),
            'public' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
        ]);

        $this->createTable('tariffs_to_billing_tariffs_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'billing_id' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('tariffs_to_billing_tariffs', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('tariffs_to_billing_tariffs', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('tariffs_to_billing_tariffs', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('tariffs_to_billing_tariffs', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_billing_tariffs', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_billing_tariffs', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('tariffs_to_connection_technologies_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'connection_technology_id' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('tariffs_to_connection_technologies', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('tariffs_to_connection_technologies', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('tariffs_to_connection_technologies', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('tariffs_to_connection_technologies', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_connection_technologies', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_connection_technologies', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('tariffs_to_opers_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'oper_id' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('tariffs_to_opers', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('tariffs_to_opers', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('tariffs_to_opers', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('tariffs_to_opers', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_opers', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_opers', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('tariffs_to_services_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('tariffs_to_services', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('tariffs_to_services', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('tariffs_to_services', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('tariffs_to_services', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_services', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('tariffs_to_services', 'cas_user_id', 'DROP DEFAULT');
    }

    public function safeDown()
    {
        echo "раскомменитровать метод надо.\n";

        /*$this->dropTable('tariffs_history');
        $this->dropTable('tariffs_to_billing_tariffs_history');
        $this->dropTable('tariffs_to_connection_technologies_history');
        $this->dropTable('tariffs_to_opers_history');
        $this->dropTable('tariffs_to_services_history');

        $this->dropColumn('tariffs_to_billing_tariffs', 'publication_status');
        $this->dropColumn('tariffs_to_billing_tariffs', 'created_at');
        $this->dropColumn('tariffs_to_billing_tariffs', 'cas_user_id');

        $this->dropColumn('tariffs_to_connection_technologies', 'publication_status');
        $this->dropColumn('tariffs_to_connection_technologies', 'created_at');
        $this->dropColumn('tariffs_to_connection_technologies', 'cas_user_id');

        $this->dropColumn('tariffs_to_opers', 'publication_status');
        $this->dropColumn('tariffs_to_opers', 'created_at');
        $this->dropColumn('tariffs_to_opers', 'cas_user_id');

        $this->dropColumn('tariffs_to_services', 'publication_status');
        $this->dropColumn('tariffs_to_services', 'created_at');
        $this->dropColumn('tariffs_to_services', 'cas_user_id');*/

        return false;
    }
}
