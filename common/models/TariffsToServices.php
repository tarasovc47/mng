<?php

namespace common\models;

use Yii;
use common\models\history\TariffsToServicesHistory;
use common\models\TariffsToConnectionTechnologies as TCT;
use yii\helpers\ArrayHelper;

class TariffsToServices extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'tariffs_to_services';
    }

    public function rules()
    {
        return [
            [['tariff_id', 'service_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['tariff_id', 'service_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tariff_id' => 'Tariff ID',
            'service_id' => 'Service ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsToServicesQuery(get_called_class());
    }

    // Настройка связей с другими таблицами
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }

    public function afterSave($insert, $changedAttributes){
        $history = new TariffsToServicesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getServicesForTariff($tariff_id, $publication_status = false)
    {
        $connection = Yii::$app->db;
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $services = $connection
                            ->createCommand("SELECT id, service_id FROM ".self::tableName()." WHERE tariff_id = '".$tariff_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::map($services, 'id', 'service_id');
    }

    public static function loadServicesListForTariffView($tariff_id){
        $connection = Yii::$app->db;
        $services = $connection
                            ->createCommand("
                                SELECT s.id, s.name
                                FROM tariffs_to_services tts
                                LEFT JOIN services s ON tts.service_id = s.id
                                WHERE tts.tariff_id = '{$tariff_id}' AND tts.publication_status = 1
                            ")
                            ->queryAll();

        
        foreach ($services as $key => $service) {
            $services[$key]['conn_techs'] = TCT::loadTechsListByService($tariff_id, $service['id']);
        }

        return $services;
    }
}
