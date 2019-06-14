<?php

use yii\db\Migration;
use yii\db\Schema;

class m171011_071117_backbone_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('backbone_nodes',[
            'id' => Schema::TYPE_PK, // int(11) auto_increment
            'ip' => Schema::TYPE_INTEGER, // ip address хранится в типе long
            'mac' => Schema::TYPE_STRING, // int(11) NULL
            'community' => Schema::TYPE_STRING, // text NULL
            'description' => Schema::TYPE_TEXT,
            'ipmon_id' => Schema::TYPE_INTEGER, // для массива, PosgreSQL
            'active' => Schema::TYPE_BOOLEAN, // для массива, PosgreSQL
            'mount_date' => Schema::TYPE_DATETIME,
            'umount_date' => Schema::TYPE_DATETIME,
        ]);
        $this->createTable('backbone_vlans',[
            'id' => Schema::TYPE_PK, // int(11) auto_increment
            'vlan' => Schema::TYPE_INTEGER, // varchar(255) NULL
            'backbone_node_id' => Schema::TYPE_INTEGER, // int(11) NULL
            'description' => Schema::TYPE_TEXT, // text NULL
            'active' => Schema::TYPE_BOOLEAN,
            'create_date' => Schema::TYPE_DATETIME,
            'destroy_date' => Schema::TYPE_DATETIME,
        ]);
        $this->createTable('backbone_hosts',[
            'id' => Schema::TYPE_PK, // int(11) auto_increment
            'vlan_id' => Schema::TYPE_INTEGER, // varchar(255) NULL
            'ipmon_id' => Schema::TYPE_INTEGER, // int(11) NULL
            'ip' => Schema::TYPE_INTEGER, // ip address хранится в типе long
            'mac' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT, // text NULL
            'active' => Schema::TYPE_BOOLEAN,
            'mount_date' => Schema::TYPE_DATETIME,
            'umount_date' => Schema::TYPE_DATETIME,
        ]);

    }

    public function safeDown()
    {
        $this->dropTable('backbone_nodes');
        $this->dropTable('backbone_vlans');
        $this->dropTable('backbone_hosts');
//        echo "m171011_071117_backbone_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171011_071117_backbone_tables cannot be reverted.\n";

        return false;
    }
    */
}
