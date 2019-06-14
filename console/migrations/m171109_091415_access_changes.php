<?php

use yii\db\Migration;
use yii\db\Query;

class m171109_091415_access_changes extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('access', 'module_setting_id', 'module_setting_key');
        $this->addColumn('modules_settings', 'uniq_key', $this->integer()->unique());

        $settings = (new Query())
            ->select(['*'])
            ->from("modules_settings")
            ->all();

        foreach($settings as $setting){
            $this->update('modules_settings', [ 'uniq_key' => $setting['id'] ], [ "id" => $setting["id"] ]);
        }
    }

    public function safeDown()
    {
        $this->renameColumn('access', 'module_setting_key', 'module_setting_id');
        $this->dropColumn('modules_settings', 'uniq_key');
    }
}
