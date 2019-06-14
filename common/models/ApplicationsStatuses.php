<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ApplicationsStatuses extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_statuses';
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
        return new \common\models\query\ApplicationsStatusesQuery(get_called_class());
    }

    public static function loadList($condition = []){
        $records = (new Query())
            ->select(['id', 'name'])
            ->from(self::tableName())
            ->orderBy(["id" => SORT_ASC]);

        if(!empty($condition)){
            $records = $records->where($condition);
        }

        $records = $records->all();

        return ArrayHelper::map($records, 'id', 'name');
    }
}
