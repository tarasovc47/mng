<?php
namespace common\models\modules_settings;

use yii\db\ActiveRecord;
class MngModulesSettings extends ActiveRecord
{
    public static function tableName()
    {
        return 'modules_settings';
    }
}