<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ZonesPorchesHistory;

class ZonesPorches extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'zones__porches';
    }

    public function rules()
    {
        return [
            [['address_id', 'porch_name', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['address_id', 'created_at', 'cas_user_id', 'updater', 'updated_at', 'publication_status'], 'integer'],
            [['porch_name'], 'string'],
            [['porch_name'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Адрес',
            'porch_name' => 'Номер подъезда',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesPorchesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ZonesPorchesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getPorchesForAddress($address_id){
        $connection = Yii::$app->db;
        $porches = $connection
                            ->createCommand("SELECT id, porch_name 
                                FROM zones__porches
                                WHERE address_id = {$address_id} AND publication_status = 1
                                ORDER BY porch_name ASC")
                            ->queryAll();
        $porches = ArrayHelper::map($porches, 'id', 'porch_name');
        natcasesort($porches);
        return $porches;
    }
}
