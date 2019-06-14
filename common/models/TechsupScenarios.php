<?php

namespace common\models;

use Yii;

class TechsupScenarios extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'techsup__scenarios';
    }

    public function rules()
    {
        return [
            [['techsup_attribute_id', 'name', 'department_id', 'status'], 'required'],
            [['techsup_attribute_id', 'department_id', 'status'], 'integer'],
            [['descr'], 'string'],
            [['descr', 'name'], 'trim'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'techsup_attribute_id' => 'Атрибут',
            'name' => 'Название',
            'department_id' => 'В какой отдел направится',
            'status' => 'Статус',
            'descr' => 'Комментарий',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TechsupScenariosQuery(get_called_class());
    }

    public function getTAttribute()
    {
        return $this->hasOne(Attributes::className(), ['id' => 'techsup_attribute_id']);
    }

    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), ['id' => 'department_id']);
    }

    public static function getStatuses($value = -1){
        $statuses = array(
            1 => "Включен",
            0 => "Отключен",
        );

        if($value > -1)
            return $statuses[$value];

        return $statuses;
    }
}
