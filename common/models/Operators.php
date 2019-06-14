<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class Operators extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'operators';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'comment' => 'Примечание',
        ];
    }

    public static function find()
    {
        return new \common\models\query\OperatorsQuery(get_called_class());
    }

    public static function loadList(){
        $connection = Yii::$app->db;
        $opers = $connection
                            ->createCommand("SELECT id, name FROM operators")
                            ->queryAll();

        return ArrayHelper::map($opers, 'id', 'name');
    }
}
