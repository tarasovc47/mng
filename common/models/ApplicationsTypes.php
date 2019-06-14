<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ApplicationsTypes extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_types';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['name'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ApplicationsTypesQuery(get_called_class());
    }

    public static function loadList(){
        $types = (new Query())
            ->select(['id', 'name'])
            ->from(self::tableName())
            ->orderBy(["id" => SORT_ASC])
            ->all();

        return ArrayHelper::map($types, 'id', 'name');
    }
}
