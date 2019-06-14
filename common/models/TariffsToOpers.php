<?php

namespace common\models;

use Yii;
use common\models\history\TariffsToOpersHistory;
use yii\helpers\ArrayHelper;


class TariffsToOpers extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'tariffs_to_opers';
    }

    public function rules()
    {
        return [
            [['tariff_id', 'oper_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['tariff_id', 'oper_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tariff_id' => 'Tariff ID',
            'oper_id' => 'Oper ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsToOpersQuery(get_called_class());
    }

    // Настройка связей с другими таблицами
    public function getOperators()
    {
        return $this->hasOne(Operators::className(), ['id' => 'oper_id']);
    }

    public function afterSave($insert, $changedAttributes){
        $history = new TariffsToOpersHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getOpersForTariff($tariff_id, $publication_status = false)
    {
        $connection = Yii::$app->db;
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $services = $connection
                            ->createCommand("SELECT id, oper_id FROM ".self::tableName()." WHERE tariff_id = '".$tariff_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::map($services, 'id', 'oper_id');
    }
}
