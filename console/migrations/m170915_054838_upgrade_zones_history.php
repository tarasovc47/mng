<?php

use yii\db\Migration;

class m170915_054838_upgrade_zones_history extends Migration
{
    public function safeUp()
    {
        $this->createTable('contact_faces_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'comment' => $this->text(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('contact_faces_emails_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'contact_face_id' => $this->integer()->notNull(),
            'email' => $this->string()->notNull(),
            'comment' => $this->string(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('contact_faces_phones_history', [
            'id' => $this->primaryKey(),
            'origin_id' => $this->integer()->notNull(),
            'contact_face_id' => $this->integer()->notNull(),
            'phone' => $this->string()->notNull(),
            'comment' => $this->string(),
            'publication_status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'cas_user_id' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('contact_faces_history');
        $this->dropTable('contact_faces_emails_history');
        $this->dropTable('contact_faces_phones_history');

        return true;
    }
}
