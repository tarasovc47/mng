<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class SearchFields extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'search__fields';
    }

    public function rules()
    {
        return [
            [['name', 'display_default_setting'], 'required'],
            [['name', 'label'], 'string'],
            [['name', 'label'], 'trim'],
            [['display_default_setting'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'label' => 'Descr',
            'display_default_setting' => 'Display Default Setting',
        ];
    }

    public static function find()
    {
        return new \common\models\query\SearchFieldsQuery(get_called_class());
    }

    public static function getFieldsList(){
        $fields = (new Query())
            ->select(['id', 'name', 'label', 'display_default_setting'])
            ->from("search__fields")
            ->orderBy('id ASC')
            ->all();

        return   ArrayHelper::index($fields, 'id');
    }
}
