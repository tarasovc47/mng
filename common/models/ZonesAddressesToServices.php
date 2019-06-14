<?php

namespace common\models;

use Yii;
use common\models\history\ZonesAddressesToServicesHistory;
use common\models\Services;
use common\models\ZonesAddressesToConnectionTechnologies as ZACT;

class ZonesAddressesToServices extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'zones__addresses_to_services';
    }

    public function rules()
    {
        return [
            [['address_id', 'service_id', 'abonent_type', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['address_id', 'service_id', 'abonent_type', 'publication_status', 'created_at', 'cas_user_id', 'updated_at', 'updater'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'service_id' => 'Service ID',
            'abonent_type' => 'Abonent Type',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesToServicesQuery(get_called_class());
    }

    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressesToServicesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public static function loadServicesListForAddress($address_id, $abonent_type, $publication_status = false){
        $where_status = '';
        if ($publication_status) {
            $where_status = " AND publication_status = '{$publication_status}'";
        }
        $connection = Yii::$app->db;
        $services = $connection
                        ->createCommand("
                                SELECT service_id
                                FROM zones__addresses_to_services
                                WHERE address_id = {$address_id} AND abonent_type = {$abonent_type}
                                ".$where_status)
                        ->queryColumn();

        return $services;
    }

    public static function loadServicesListForZonesAddressView($address_id, $abonent_type){
        $connection = Yii::$app->db;
        $services = $connection
                            ->createCommand("
                                SELECT s.id, s.name
                                FROM zones__addresses_to_services zats
                                LEFT JOIN services s ON zats.service_id = s.id
                                WHERE zats.address_id = '{$address_id}' AND zats.abonent_type = {$abonent_type} AND zats.publication_status = 1
                            ")
                            ->queryAll();

        
        foreach ($services as $key => $service) {
            $services[$key]['conn_techs'] = ZACT::loadTechsListByService($address_id, $service['id'], $abonent_type);
        }

        return $services;
    }
}
