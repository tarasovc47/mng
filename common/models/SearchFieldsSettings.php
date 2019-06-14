<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class SearchFieldsSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search__fields_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id', 'field_id', 'cas_user_id', 'value'], 'required'],
            [['department_id', 'field_id', 'value', 'cas_user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id' => 'Department ID',
            'field_id' => 'Field ID',
            'value' => 'Value',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return SearchFieldsSettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\SearchFieldsSettingsQuery(get_called_class());
    }

    public static function getValuesList($department_id, $cas_user_id){
        $fields_values = (new Query())
            ->select(['field_id', 'value'])
            ->from("search__fields_settings")
            ->where(['department_id' => $department_id, 'cas_user_id' => $cas_user_id])
            ->all();

        return ArrayHelper::map($fields_values, 'field_id', 'value');
    }

    public static function getModelId($field_id, $department_id, $cas_user_id){
        return (new Query())
            ->select(['id'])
            ->from("search__fields_settings")
            ->where(['field_id' => $field_id, 'department_id' => $department_id, 'cas_user_id' => $cas_user_id])
            ->one();
    }
}
