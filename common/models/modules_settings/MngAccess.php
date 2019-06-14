<?php

namespace common\models\modules_settings;

use yii\db\ActiveRecord;
class MngAccess extends ActiveRecord
{
    public static function tableName()
    {
        return 'access';
    }
}