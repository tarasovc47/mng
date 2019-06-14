<?php

use yii\db\Migration;

class m170918_044546_zones__manag_companies_history extends Migration
{
    public function safeUp()
    {
        $this->createTable('manag_companies_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'jur_address_id' => $this->string()->notNull(),
            'actual_address_id' => $this->string()->notNull(),
            'coordinates' => $this->string(),
            'comment' => $this->text(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'company_type' => $this->integer()->notNull(),
            'abonent' => $this->integer(),
        ]);

        $this->createTable('manag_companies_branches_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'actual_address_id' => $this->string()->notNull(),
            'coordinates' => $this->string(),
            'comment' => $this->text(),
            'company_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('manag_companies_to_contacts_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'branch_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
            'publication_status' => $this->integer()->notNull(),
            'contact_face_id' => $this->integer()->notNull(),
            'contact_office_id' => $this->integer()->notNull(),
            'comment' => $this->text(),
        ]);

        $this->createTable('manag_companies_types_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'short_name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->addColumn('manag_companies_types', 'publication_status', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('manag_companies_types', 'created_at', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('manag_companies_types', 'cas_user_id', $this->integer()->notNull()->defaultValue(43));

        $this->alterColumn('manag_companies_types', 'publication_status', 'DROP DEFAULT');
        $this->alterColumn('manag_companies_types', 'created_at', 'DROP DEFAULT');
        $this->alterColumn('manag_companies_types', 'cas_user_id', 'DROP DEFAULT');
    }

    public function safeDown()
    {
        echo "раскомменитровать метод надо.\n";

        /*$this->dropTable('manag_companies_history');
        $this->dropTable('manag_companies_branches_history');
        $this->dropTable('manag_companies_to_contacts_history');
        $this->dropTable('manag_companies_types_history');

        $this->dropColumn('manag_companies_types', 'publication_status');
        $this->dropColumn('manag_companies_types', 'created_at');
        $this->dropColumn('manag_companies_types', 'cas_user_id');*/

        return false;
    }
}
