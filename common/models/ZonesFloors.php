<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ZonesFloorsHistory;

class ZonesFloors extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'zones__floors';
    }

    public function rules()
    {
        return [
            [['porch_id', 'floor_name', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['porch_id'], 'string'],
            [['porch_id'], 'trim'],
            [['floor_name', 'created_at', 'cas_user_id', 'updater', 'updated_at', 'publication_status'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'porch_id' => 'Подъезд',
            'floor_name' => 'Этаж',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesFloorsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ZonesFloorsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getFloorsForPorches($porch_id){
        $connection = Yii::$app->db;
        $floors = $connection
                            ->createCommand("SELECT id, floor_name 
                                FROM zones__floors
                                WHERE porch_id = '{$porch_id}' AND publication_status = 1
                                ORDER BY floor_name DESC")
                            ->queryAll();
        $floors = ArrayHelper::map($floors, 'id', 'floor_name');
        natcasesort($floors);
        return $floors;
    }
}
