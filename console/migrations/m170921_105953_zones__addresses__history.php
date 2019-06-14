<?php

use yii\db\Migration;

class m170921_105953_zones__addresses__history extends Migration
{
    public function safeUp()
    {
        $this->createTable('zones__address_statuses_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'tariffs_required' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__address_statuses', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__address_statuses', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__address_statuses', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__address_statuses', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__address_statuses', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__address_statuses', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__address_types_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__address_types', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__address_types', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__address_types', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__address_types', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__address_types', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__address_types', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__addresses_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'address_uuid' => $this->string()->notNull(),
            'all_flats' => $this->integer(),
            'address_type_id' => $this->integer(),
            'comment' => $this->text(),
            'district_id' => $this->integer(),
            'area_id' => $this->integer(),
            'build_status_individual' => $this->integer()->notNull(),
            'manag_company_id' => $this->integer(),
            'build_status_entity' => $this->integer()->notNull(),
            'coordinates' => $this->string(),
            'connection_cost_individual' => $this->string(),
            'connection_cost_entity' => $this->string(),
            'all_offices' => $this->integer(),
            'manag_company_branch_id' => $this->integer(),
            'key_keeper' => $this->integer(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'contract_with_manag_company' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__addresses', 'publication_status', $this->integer()->notNull()->defaultValue(1));

        $this->alterColumn('zones__addresses', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses', 'coordinates', 'DROP NOT NULL');

        $this->createTable('zones__addresses_to_agreements_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'agreement_id' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__addresses_to_agreements', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__addresses_to_agreements', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__addresses_to_agreements', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__addresses_to_agreements', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_agreements', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_agreements', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__addresses_to_connection_technologies_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'connection_technology_id' => $this->integer()->notNull(),
            'abonent_type' => $this->integer()->notNull(),
            'auto_tariffs' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__addresses_to_connection_technologies', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__addresses_to_connection_technologies', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__addresses_to_connection_technologies', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__addresses_to_connection_technologies', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_connection_technologies', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_connection_technologies', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__addresses_to_opers_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'oper_id' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__addresses_to_opers', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__addresses_to_opers', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__addresses_to_opers', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__addresses_to_opers', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_opers', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_opers', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__addresses_to_services_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'abonent_type' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__addresses_to_services', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__addresses_to_services', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__addresses_to_services', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__addresses_to_services', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_services', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_services', 'cas_user_id', 'DROP DEFAULT');

        $this->createTable('zones__addresses_to_tariffs_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'abonent_type' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
        ]);

        $this->addColumn('zones__addresses_to_tariffs', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__addresses_to_tariffs', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__addresses_to_tariffs', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('zones__addresses_to_tariffs', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_tariffs', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__addresses_to_tariffs', 'cas_user_id', 'DROP DEFAULT');

        $this->addColumn('zones__districts_and_areas', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('zones__districts_and_areas', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('zones__districts_and_areas', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));
        $this->alterColumn('zones__districts_and_areas', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('zones__districts_and_areas', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('zones__districts_and_areas', 'cas_user_id', 'DROP DEFAULT');

        $this->addColumn('zones__districts_and_areas_history', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->alterColumn('zones__districts_and_areas_history', 'publication_status', 'DROP DEFAULT');
    }

    public function safeDown()
    {
        echo "раскомменитровать метод надо.\n";

        /*$this->dropTable('zones__address_statuses_history');
        $this->dropColumn('zones__address_statuses', 'publication_status');
        $this->dropColumn('zones__address_statuses', 'created_at');
        $this->dropColumn('zones__address_statuses', 'cas_user_id');

        $this->dropTable('zones__address_types_history');
        $this->dropColumn('zones__address_types', 'publication_status');
        $this->dropColumn('zones__address_types', 'created_at');
        $this->dropColumn('zones__address_types', 'cas_user_id');

        $this->dropTable('zones__addresses_history');
        $this->dropColumn('zones__addresses', 'publication_status');
        //не написана отмена для alterColumn таблицы zones__addresses

        $this->dropTable('zones__addresses_to_agreements_history');
        $this->dropColumn('zones__addresses_to_agreements', 'publication_status');
        $this->dropColumn('zones__addresses_to_agreements', 'created_at');
        $this->dropColumn('zones__addresses_to_agreements', 'cas_user_id');

        $this->dropTable('zones__addresses_to_connection_technologies_history');
        $this->dropColumn('zones__addresses_to_connection_technologies', 'publication_status');
        $this->dropColumn('zones__addresses_to_connection_technologies', 'created_at');
        $this->dropColumn('zones__addresses_to_connection_technologies', 'cas_user_id');

        $this->dropTable('zones__addresses_to_opers_history');
        $this->dropColumn('zones__addresses_to_opers', 'publication_status');
        $this->dropColumn('zones__addresses_to_opers', 'created_at');
        $this->dropColumn('zones__addresses_to_opers', 'cas_user_id');

        $this->dropTable('zones__addresses_to_services_history');
        $this->dropColumn('zones__addresses_to_services', 'publication_status');
        $this->dropColumn('zones__addresses_to_services', 'created_at');
        $this->dropColumn('zones__addresses_to_services', 'cas_user_id');

        $this->dropTable('zones__addresses_to_tariffs_history');
        $this->dropColumn('zones__addresses_to_tariffs', 'publication_status');
        $this->dropColumn('zones__addresses_to_tariffs', 'created_at');
        $this->dropColumn('zones__addresses_to_tariffs', 'cas_user_id');

        $this->dropColumn('zones__districts_and_areas_history', 'publication_status');
        
        $this->dropColumn('zones__districts_and_areas', 'publication_status');
        $this->dropColumn('zones__districts_and_areas', 'created_at');
        $this->dropColumn('zones__districts_and_areas', 'cas_user_id');*/

        return false;
    }
}
