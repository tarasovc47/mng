<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ManagCompaniesTypesHistory;

class ManagCompaniesTypes extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'manag_companies_types';
    }

    public function rules()
    {
        return [
            [['name', 'short_name', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
            [['name', 'comment', 'short_name'], 'string'],
            [['name', 'comment', 'short_name'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'short_name' => 'Сокращённое название',
            'comment' => 'Примечание',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ManagCompaniesTypesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ManagCompaniesTypesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getTypesList(){
        $connection = Yii::$app->db;
        $types = $connection
                            ->createCommand("SELECT id, name FROM manag_companies_types")
                            ->queryAll();

        return ArrayHelper::map($types, 'id', 'name');
    }
}
