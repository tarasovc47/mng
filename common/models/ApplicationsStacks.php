<?php

namespace common\models;

use Yii;

class ApplicationsStacks extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_stacks';
    }

    public function getApplications()
    {
        return $this->hasMany(Applications::className(), ['application_stack_id' => 'id']);
    }

    public function rules()
    {
        return [
            [['id', 'created_at'], 'required'],
            [['created_at'], 'integer'],
            [['id'], 'string', 'max' => 255],
            [['id'], 'trim'],
            [['id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'created_at' => 'Дата создания',
        ];
    }

    public function generateId($created_at){
        $year = date("Y", $created_at);
        $last = self::find()->orderBy(['created_at' => SORT_DESC])->one();
        $code = false;

        if($last){
            $last_code_parse = explode("/", $last->id);
            $number = (int)$last_code_parse[2];

            if($year == $last_code_parse[1]){
                $number++;
                $number = str_pad($number, 9, "0", STR_PAD_LEFT);

                $code = "Z/" . $year . "/" . $number;
            }
        }

        if(!$code){
            $code = "Z/" . $year . "/000000001";
        }

        $this->id = $code;
        return true;
    }
}
