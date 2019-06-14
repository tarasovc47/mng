<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class GlobalServices extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'global_services';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
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
        return new \common\models\query\GlobalServicesQuery(get_called_class());
    }

    public static function loadList(){
        $services = (new Query())
            ->select(['id', 'name'])
            ->from('global_services')
            ->orderBy(["id" => SORT_ASC])
            ->all();

        $services[] = [
            'id' => 0,
            'name' => 'Не отмечено',
        ];

        return ArrayHelper::map($services, 'id', 'name');
    }
}
