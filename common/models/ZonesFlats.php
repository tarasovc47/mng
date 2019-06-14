<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ZonesFlatsHistory;

class ZonesFlats extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'zones__flats';
    }

    public function rules()
    {
        return [
            [['flat_name', 'floor_id', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['flat_name'], 'string'],
            [['flat_name'], 'trim'],
            [['floor_id', 'created_at', 'cas_user_id', 'updater', 'updated_at', 'publication_status'], 'integer'],
            [['flat_name'], 'integer', 'on' => 'flat_range'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'flat_name' => 'Квартира/Офис',
            'floor_id' => 'Этаж',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesFlatsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ZonesFlatsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getFlatsForPorch($porch_id, $room_type){
        $connection = Yii::$app->db;
        $apartments = $connection
                            ->createCommand("SELECT fta.id, fta.floor_id, fta.flat_name 
                                FROM zones__flats fta
                                LEFT JOIN zones__floors ptf ON fta.floor_id = ptf.id
                                WHERE ptf.porch_id = '{$porch_id}' AND fta.room_type = {$room_type} AND fta.publication_status = 1
                                ORDER BY fta.flat_name ASC")
                            ->queryAll();
        $apartments = ArrayHelper::map($apartments, 'id', 'flat_name', 'floor_id');
        foreach ($apartments as $key => $floor) {
            natcasesort($apartments[$key]);
        }
        return $apartments;
    }
}
